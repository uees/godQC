# -- coding: utf-8 -*-

from sqlalchemy import TIMESTAMP, Column, Integer, String, Text, text
from sqlalchemy.dialects.mysql import INTEGER
from sqlalchemy.orm import relationship

from .base import Base


class ProductBatch(Base):

    __tablename__ = 'product_batches'
    __table_args__ = {
        "mysql_charset": "utf8mb4"
    }

    id = Column(Integer, primary_key=True, autoincrement=True)
    product_name = Column(String(128))
    product_name_suffix = Column(String(64), nullable=True)
    batch_number = Column(String(32))
    type = Column(String(32))
    amount = Column(Integer, nullable=True)
    tests_num = Column(INTEGER(unsigned=True), default=0)
    memo = Column(Text, nullable=True)
    created_at = Column(TIMESTAMP(True), nullable=True, server_default=text('CURRENT_TIMESTAMP'))
    updated_at = Column(TIMESTAMP(True), nullable=True)

    test_records = relationship("QCRecord", back_populates="product_batch")
