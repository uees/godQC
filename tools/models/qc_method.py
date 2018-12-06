# -- coding: utf-8 -*-

from sqlalchemy import (TIMESTAMP, Column, Integer, String,
                        Text, text)

from .base import Base


class QCMethod(Base):

    __tablename__ = 'test_methods'
    __table_args__ = {
        "mysql_charset": "utf8"
    }

    id = Column(Integer, primary_key=True, autoincrement=True)
    name = Column(String(256))
    file = Column(String(256), nullable=True)
    content = Column(Text, nullable=True)
    created_at = Column(TIMESTAMP(True), nullable=True, server_default=text('CURRENT_TIMESTAMP'))
    updated_at = Column(TIMESTAMP(True), nullable=True)
