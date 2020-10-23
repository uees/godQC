from models.base import db_session
from models.product import Product
from models.product_batch import ProductBatch
from models.qc_record import QCRecord


def get_record_by_id(record_id):
    return QCRecord.query.filter_by(id=record_id).first()


def get_record_by_batch(batch):
    return QCRecord.query.join(ProductBatch).filter(ProductBatch.batch_number == batch).first()


def get_product_by_internal_name(internal_name):
    return Product.query.filter_by(internal_name=internal_name).first()


def get_product_by_record(record: QCRecord):
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


def set_record_created_doc(record: QCRecord):
    record.is_created_doc = True
    db_session.add(record)
    db_session.commit()
