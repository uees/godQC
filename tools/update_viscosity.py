import os

from models.base import db_session
from models.product import Product
from report.common import get_products_form_xls
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
        db_session.add(product)
        print(f"更新了产品{p['internal_name']}")

    db_session.commit()


if __name__ == "__main__":
    database_filepath = os.path.join(ROOT_PATH, "basedata/database.xlsx")
    update_product_viscosity(database_filepath)
