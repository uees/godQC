# -*- coding:utf-8 -*-
from celery.utils.log import get_task_logger

from .celery import app
from .generator import Generator
from .service import get_record_by_id

logger = get_task_logger(__name__)


@app.task
def make_report(record_id):
    record = get_record_by_id(record_id)
    product_batch = record.product_batch
    generator = Generator(record)
    generator.run()
    logger.info('生成报告： {}_{}'.format(product_batch.product_name, product_batch.batch_number))
