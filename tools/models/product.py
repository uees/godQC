# -- coding: utf-8 -*-

from sqlalchemy import (TIMESTAMP, Column, Integer, String,
                        Text, text)

from .base import Base


class Product(Base):

    __tablename__ = 'products'

    id = Column(Integer, primary_key=True, autoincrement=True)
    internal_name = Column(String(256))
    market_name = Column(String(256))
    part_a = Column(String(256), nullable=True)
    part_b = Column(String(256), nullable=True)
    ab_ratio = Column(String(256), nullable=True)
    color = Column(String(256), nullable=True)
    spec = Column(String(256), nullable=True)
    label_viscosity = Column(String(256), nullable=True)
    viscosity_width = Column(String(256), nullable=True)
    metas = Column(Text, nullable=True)
    created_at = Column(TIMESTAMP(True), nullable=True, server_default=text('CURRENT_TIMESTAMP'))
    updated_at = Column(TIMESTAMP(True), nullable=True)
