import json

from sqlalchemy import (TIMESTAMP, Boolean, Column, ForeignKey, Integer,
                        SmallInteger, String, Text, UniqueConstraint, text)
from sqlalchemy.dialects.mysql import INTEGER
from sqlalchemy.orm import relationship, synonym

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
    is_archived = Column(Boolean, default=False)
    is_created_doc = Column(Boolean, default=False)
    push_state = Column(String(128), default="no_push")
    created_at = Column(TIMESTAMP(True), nullable=True, server_default=text('CURRENT_TIMESTAMP'))
    updated_at = Column(TIMESTAMP(True), nullable=True)

    product_batch = relationship("ProductBatch", back_populates="test_records", lazy='joined')
    record_items = relationship("QCRecordItem", back_populates="test_record", lazy="select")

    def has_item(self, name):
        """判断是否有指定的检测项目"""
        for item in self.record_items:
            if item.item == name:
                return True

        return False


class QCRecordItem(Base):
    __tablename__ = 'test_record_items'
    __table_args__ = (
        UniqueConstraint("test_record_id", "item"),
        {
            "mysql_charset": "utf8mb4",
        }
    )

    id = Column(Integer, primary_key=True, autoincrement=True)
    test_record_id = Column(INTEGER(unsigned=True), ForeignKey('test_records.id'))
    item = Column(String(64))
    _spec = Column("spec", Text, nullable=True)
    value = Column(String(128), nullable=True)
    fake_value = Column(String(128), nullable=True)
    conclusion = Column(String(32), nullable=True)
    tester = Column(String(64), nullable=True)
    created_at = Column(TIMESTAMP(True), nullable=True, server_default=text('CURRENT_TIMESTAMP'))
    updated_at = Column(TIMESTAMP(True), nullable=True)

    test_record = relationship(QCRecord, back_populates="record_items")

    def _get_spec(self):
        if self._spec is not None:
            return json.loads(self._spec)

    def _set_spec(self, spec):
        self._spec = json.dumps(spec)

    spec = synonym("_spec", descriptor=property(_get_spec, _set_spec))

    def get_spec(self) -> str:
        """获取规格要求"""
        spec = self._get_spec()  # 注意 spec 是字典
        result = ""
        if 'unit' not in spec['data']:  # undefined to ''
            spec['data']['unit'] = ''

        if spec['value_type'] == "RANGE":
            if spec['data']['min']:
                result += f"≥ {spec['data']['min']}{spec['data']['unit']}, "
            if spec['data']['max']:
                result += f"≤ {spec['data']['max']}{spec['data']['unit']} "

        elif spec['value_type'] == 'INFO' or spec['value_type'] == 'NUMBER' or not spec['value_type']:
            if spec['data']['value']:
                result = spec['data']['value']
            if spec['data']['unit']:
                result += spec['data']['unit']

        elif spec['value_type'] == 'ONLY_SHOW':
            if spec['data']['value']:
                tmp_arr = spec['data']['value'].split('|', 1)
                result = tmp_arr[0]

        return result
