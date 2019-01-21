# -*- coding:utf-8 -*-
from celery.utils.log import get_task_logger
from DictObject import DictObject

from .celery import app
from .generator import Generator

logger = get_task_logger(__name__)


@app.task
def make_report(product, batch, tips=None, options=None):
    logger.info('生成报告： {}_{}'.format(product, batch))

    generator = Generator(DictObject({
        'name': product,
        'batch': batch,
        'tips': tips,
        'options': options
        }))
    generator.generate_report()
