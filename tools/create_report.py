from models.product_batch import ProductBatch
from models.qc_record import QCRecord
from report.generator import Generator


def run():
    #for record in QCRecord.query.filter(QCRecord.is_archived == True).filter(QCRecord.is_created_doc == False).limit(10).all():  # noqa
    #    Generator(record).run()

    # fix 8BKT11
    for record in QCRecord.query.join(ProductBatch).filter(QCRecord.is_archived == True).\
        filter(ProductBatch.product_name == "8BKT11").filter(QCRecord.created_at >= '2020-10-01').all():  # noqa
        Generator(record).run()


if __name__ == "__main__":
    run()
