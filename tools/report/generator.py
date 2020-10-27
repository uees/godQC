import os
import random
from datetime import datetime
from glob import glob

from models.qc_record import QCRecord
from settings import SPA_ROOT, TEMPLATES_ROOT

from celery.utils.log import get_task_logger

from . import service
from .common import make_viscosity, make_viscosity_limit, today_reports_root
from .pdf import add_watermark, create_watermark
from .word import WordTemplate

logger = get_task_logger(__name__)


class Generator(object):
    """ 检验报告生成器 """

    def __init__(self, record: QCRecord):
        self.reports_root = today_reports_root()
        self.record = record
        self.product = service.get_product_by_record(record)

    def run(self):
        templates = self.get_templates()

        for template in templates:
            if template.get("name") == 'pdf':
                options = template.get("options") if isinstance(template.get("options"), dict) else {}
                templates_dir = options.get("templates_dir")
                if templates_dir:
                    self.generate_pdf(templates_dir)

            else:
                self.generate_report(template)

        service.set_record_created_doc(self.record)

    def generate_report(self, template):
        template_path = self.get_template_path(template.get("name"))
        context = self.make_context(template)

        wt = WordTemplate(template_path)
        wt.replace(context)

        report_file = self.get_report_filepath(template)
        if os.path.exists(report_file):
            logger.warning("{}已经存在了".format(report_file))
        else:
            wt.save(report_file)

    def generate_pdf(self, templates_dir=None):
        basename = self.product.market_name.replace(' ', '')

        if not templates_dir:
            templates_dir = basename

        files = glob("*.pdf")
        f_name = random.choice(files)
        f_path = os.path.join(SPA_ROOT, os.path.join(templates_dir, f_name))

        out_f_name = '%s-%s.pdf' % (basename, self.record.product_batch.batch_number)
        out_f_path = os.path.join(self.reports_root, out_f_name)

        watermark = create_watermark(out_f_name, SPA_ROOT)
        add_watermark(watermark, f_path, out_f_path)

    def get_report_filepath(self, template):
        qc_date = datetime.strftime(datetime.now(), '%Y%m%d')
        batch = self.record.product_batch

        tips = template.get("tips")
        if isinstance(tips, list):
            tips = '-'.join(tips)

        _, extension = os.path.splitext(template.get("name"))

        name = '_'.join([batch.batch_number, self.product.internal_name, tips])

        options = template.get("options") if isinstance(template.get("options"), dict) else {}
        if options.get("customers", "").find("深南") >= 0:
            flag = "{}容大{}COC_{}".format(qc_date, self.product.market_name, batch.batch_number)
            filename = "{}_{}{}".format(name, flag, extension)
        else:
            filename = '{}{}'.format(name, extension)

        return os.path.join(self.reports_root, filename)

    def make_context(self, template):
        context = self.product.to_dict()
        context['qc_date'] = datetime.strftime(datetime.now(), '%Y/%m/%d')
        context['batch'] = self.record.product_batch.batch_number

        # get self.record qc values
        # viscosity, viscosity_limit
        context['viscosity_limit'], context['viscosity'] = self.get_record_item('粘度')
        context["exposure_spec"], context["exposure"] = self.get_record_item("感光性")

        # ftir
        # context['ftir'] = '{}%'.format(round(random.uniform(99.1, 99.8), 2))
        _, context['ftir'] = self.get_record_item("红外匹配度")
        # 达因要求
        context['dayinReq'], context['dayinVal'] = self.get_record_item('表面张力')

        options = template.get("options", {})
        if options:
            if options.get('dayinReq'):  # dayinReq 达因要求
                context['dayinReq'] = options.get('dayinReq')
                context['dayinVal'] = options.get('dayinVal')  # dayinVal 达因值

            if options.get('customer_code'):  # customer_code 物料编码
                context['customer_code'] = options.get('customer_code')

            if options.get('viscosity_limit'):  # viscosity_limit
                context['viscosity_limit'] = options.get('viscosity_limit')
                context['viscosity'] = options.get('viscosity') if options.get('viscosity') else make_viscosity(context['viscosity_limit'])

            if options.get('shuanzhi'):  # shuanzhi 酸值
                context['shuanzhi'] = options.get('shuanzhi')

        self.check_context(context)

        return context

    def check_context(self, context):
        if 'customer_code' not in context:
            context['customer_code'] = ''

        if context["category_id"] in [6, 7]:  # 内外层湿膜级数fix
            if float(context["exposure"]) < 5:
                context["exposure"] = 5
            elif float(context["exposure"]) > 8:
                context["exposure"] = 8
        elif context["category_id"] in [2, 3, 4, 18]:  # 阻焊级数fix
            if float(context["exposure"]) > 11:
                context["exposure"] = 11
            elif float(context["exposure"]) < 9:
                context["exposure"] = 9

    def get_record_item(self, name) -> (str, str):
        """
        获取检测项目的值
        """
        spec, value = '', ''

        # 关于粘度有混合粘度的优先获取混合粘度
        if name == "粘度" and self.record.has_item("混合粘度"):
            name = "混合粘度"

        if name == "粘度":
            # 从 product 获取粘度范围标准
            spec, value = make_viscosity_limit(self.product)

        for item in self.record.record_items:
            if item.item == name:
                # 不合格的获取 fake_value
                value = item.fake_value if item.conclusion == "NG" else item.value
                if not value:
                    value = 'PASS'

                # 保证粘度范围标准都是从 product 获取
                if name != "粘度":
                    spec = item.get_spec()

                break

        return spec, value

    def get_templates(self):
        if not self.product:  # fix
            return []

        return service.get_product_templates(self.product)

    def get_template_path(self, template_file):
        return os.path.join(TEMPLATES_ROOT, template_file)
