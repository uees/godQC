from models.qc_record import QCRecord
from report.generator import Generator


def run():
    for record in QCRecord.query.filter(QCRecord.is_archived == True).filter(QCRecord.is_created_doc == False).limit(10).all():  # noqa
        Generator(record).run()


if __name__ == "__main__":
    run()
