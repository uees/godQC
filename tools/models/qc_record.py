# -- coding: utf-8 -*-

from sqlalchemy import (TIMESTAMP, Boolean, Column, ForeignKey, Integer,
                        SmallInteger, String, Text, text)
from sqlalchemy.dialects.mysql import INTEGER
from sqlalchemy.orm import relationship

from .base import Base


class QCRecord(Base):

    __tablename__ = 'test_records'
    __table_args__ = {
        "mysql_charset": "utf8mb4"
    }

    id = Column(Integer, primary_key=True, autoincrement=True)
    product_batch_id = Column(INTEGER(unsigned=True), ForeignKey('product_batches.id'))
    test_times = Column(SmallInteger, default=1)
    conclusion = Column(String(64), nullable=True)
    testers = Column(String(256), nullable=True)
    completed_at = Column(TIMESTAMP(True), nullable=True)
    said_package_at = Column(TIMESTAMP(True), nullable=True)
    memo = Column(Text, nullable=True)
    show_reality = Column(Boolean, default=False)
    created_at = Column(TIMESTAMP(True), nullable=True, server_default=text('CURRENT_TIMESTAMP'))
    updated_at = Column(TIMESTAMP(True), nullable=True)

    product_batch = relationship("ProductBatch", back_populates="test_records")
