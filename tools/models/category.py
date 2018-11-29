# -- coding: utf-8 -*-

from sqlalchemy import (TIMESTAMP, Column, Integer, String,
                        Text, text)

from .base import Base


class Category(Base):

    __tablename__ = 'categories'

    id = Column(Integer, primary_key=True, autoincrement=True)
    name = Column(String(256))
    slug = Column(String(256))
    memo = Column(Text, nullable=True)
    created_at = Column(TIMESTAMP(True), nullable=True, server_default=text('CURRENT_TIMESTAMP'))
    updated_at = Column(TIMESTAMP(True), nullable=True)
