import os
import random
from datetime import datetime
from glob import glob

from settings import SPA_ROOT, TEMPLATES_ROOT

from celery.utils.log import get_task_logger

from . import service
from .common import today_reports_root
from .pdf import add_watermark, create_watermark
from .word import WordTemplate

logger = get_task_logger(__name__)


class Generator(object):
    """ 检验报告生成器 """

    def __init__(self, record):
        self.reports_root = today_reports_root()
        self.record = record
        self.product = service.get_record_product(self.record)

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

    def generate_report(self, template):
        template_path = self.get_template_path(template.get("name"))
        context = self.make_context(template)

        tp = WordTemplate(template_path)
        tp.replace(context)

        report_file = self.report_filename(template)
        if os.path.exists(report_file):
            logger.warning("{}已经存在了".format(report_file))
        else:
            tp.save(report_file)

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

    def report_filename(self, template):
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
        context['ftir'] = '{}%'.format(round(random.uniform(99.1, 99.8), 2))

        # todo template.get("options", {}) merge

        return context

    def get_templates(self):
        return service.get_product_templates(self.product)

    def get_template_path(self, template_file):
        return os.path.join(TEMPLATES_ROOT, template_file)
