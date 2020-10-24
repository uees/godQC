"""
更新本地数据库中的产品相关信息
"""
import os

from sqlalchemy import or_

from models.base import db_session
from models.product import Product
from report.common import get_products_form_xls, write_products_xls
from settings import ROOT_PATH


def update_product_viscosity(database_filepath):
    _, products = get_products_form_xls(database_filepath)
    for p in products:
        product = Product.query.filter_by(internal_name=p["internal_name"]).first()
        if not product:
            print(f"无效的产品{p['internal_name']}")
            continue

        product.label_viscosity = p["label_viscosity"]
        product.viscosity_width = p["viscosity_width"]
        product.market_name = p["market_name"]
        db_session.add(product)
        print(f"更新了产品{product.internal_name}")

    db_session.commit()


def get_not_label_viscosity_products(database_filepath):
    products = Product.query.filter(or_(Product.label_viscosity == None, Product.label_viscosity == "")).all()  # noqa

    write_products_xls(database_filepath, 'products', products)


if __name__ == "__main__":
    update_product_viscosity(os.path.join(ROOT_PATH, "basedata/database.xlsx"))

    get_not_label_viscosity_products(os.path.join(ROOT_PATH, "basedata/database_temp.xlsx"))
