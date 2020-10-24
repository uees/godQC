"""
celery -A tasks worker -l info -P eventlet  -c 1000
"""
from celery import Celery
from celery.utils.log import get_task_logger

from report.generator import Generator
from report.service import get_record_by_id

logger = get_task_logger(__name__)

app = Celery('tasks')
app.config_from_object('report.celeryconfig')


@app.task
def make_report(record_id):
    record = get_record_by_id(record_id)
    generator = Generator(record)
    generator.run()

    # product_batch = record.product_batch
    # logger.info('生成报告： {}_{}'.format(product_batch.product_name, product_batch.batch_number))
