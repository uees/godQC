# -- coding: utf-8 -*-
from datetime import datetime

from models.base import db_session
from models.product import Product
from models.qc_record import QCRecord


def get_record_by_id(record_id):
    return QCRecord.query.filter_by(id=record_id).first()


def get_product_by_internal_name(internal_name):
    return Product.query.filter_by(internal_name=internal_name).first()


def get_record_product(record):
    product_name = record.product_batch.product_name
    return get_product_by_internal_name(product_name)


def get_product_templates(product):
    category_metas = product.category.metas
    product_metas = product.metas

    templates = []
    category_templates = category_metas.get("templates", []) if isinstance(category_metas, dict) else []
    product_templates = product_metas.get("templates", []) if isinstance(product_metas, dict) else []

    if hasattr(product_metas, "cancel_category_template") and product_metas.cancel_category_template:
        templates = product_templates
    else:
        templates = _merge_templates(category_templates, product_templates)

    return templates


def _merge_templates(category_templates, product_templates):
    """ 模板合并, product_templates 会覆盖 category_templates 中的同名项 """
    for template in category_templates:
        if not _has_template(product_templates, template):
            product_templates.append(template)

    return product_templates


def _has_template(templates, template):
    for element in templates:
        if element.name == template.name:
            return True

    return False


def set_record_reported(record):
    metas = record.metas

    if not metas:
        metas = {}

    metas.has_report = True
    metas.report_date = datetime.strftime(datetime.now(), '%Y-%m-%d')

    record.metas = metas
    db_session.add(record)

    # 不自动提交会话
    # db_session.commit()
