# -- coding: utf-8 -*-

import unittest

from models.product import Product
from models.qc_record import QCRecord
from report import service
from report.generator import Generator


class ReportTestCase(unittest.TestCase):

    def setUp(self):
        pass

    def tearDown(self):
        pass

    def test_service(self):
        record = service.get_record_by_id(-1)
        self.assertIsNone(record)

        record = service.get_record_by_id(20)
        self.assertIsInstance(record, QCRecord)
        self.assertEqual(record.product_batch.product_name, "8G04")

        product = service.get_record_product(record)
        self.assertIsInstance(product, Product)
        self.assertEqual(product.internal_name, "8G04")

        templates = service.get_product_templates(product)
        self.assertIsInstance(templates, list)

    def test_generator(self):
        records = QCRecord.query.limit(10).all()

        for record in records:
            Generator(record).run()


if __name__ == '__main__':
    unittest.main()
