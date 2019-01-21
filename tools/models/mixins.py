# -- coding: utf-8 -*-
import json

from sqlalchemy import Column, Text
from sqlalchemy.orm import synonym


class MetaMixin(object):
    _metas = Column('metas', Text, nullable=True)

    def _get_metas(self):
        return json.loads(self._metas)

    def _set_metas(self, metas):
        self._metas = json.dumps(metas)

    metas = synonym("_metas", descriptor=property(_get_metas, _set_metas))
