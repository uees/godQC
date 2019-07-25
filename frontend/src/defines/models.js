export function Category() {
  return {
    id: undefined,
    name: undefined,
    slug: undefined,
    memo: undefined,
    metas: undefined,
    products: undefined,
    testWay: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function Customer() {
  return {
    id: undefined,
    name: undefined,
    address: undefined,
    contacts: undefined,
    tel: undefined,
    requirements: undefined,
    products: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function CustomerRequirement() {
  return {
    id: undefined,
    customer_id: undefined,
    product_id: undefined,
    customer: undefined,
    product: undefined,
    item: undefined,
    spec: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function DisqualificationStatistics() {
  return {
    id: undefined,
    year: undefined,
    month: undefined,
    qc_type: undefined,
    category_id: undefined,
    category: undefined,
    item: undefined,
    amount: undefined,
    rate: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function H8100PatternTest() {
  return {
    id: undefined,
    product_name: undefined,
    batch_number: undefined,
    nai_han_xing: undefined,
    nai_rong_ji: undefined,
    nai_suan_jian: undefined,
    h12_xian_ying: undefined,
    h24_xian_ying: undefined,
    tester: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function A9060PatternTest() {
  return {
    id: undefined,
    product_name: undefined,
    batch_number: undefined,
    ge_ye_xian_ying: undefined,
    ge_ye_bao_guang: undefined,
    die_ban: undefined,
    lao_hua: undefined,
    tester: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function Product() {
  return {
    id: undefined,
    category_id: undefined,
    test_way_id: undefined,
    category: undefined,
    internal_name: undefined,
    market_name: undefined,
    part_a: undefined,
    part_b: undefined,
    ab_ratio: undefined,
    color: undefined,
    spec: undefined,
    label_viscosity: undefined,
    viscosity_width: undefined,
    metas: {},
    customers: undefined,
    testWay: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function ProductBatch() {
  return {
    id: undefined,
    product_name: undefined,
    product_name_suffix: undefined,
    batch_number: undefined,
    type: undefined,
    amount: undefined,
    tests_num: undefined,
    memo: undefined,
    testRecords: undefined,
    disposes: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function ProductDispose() {
  return {
    id: undefined,
    product_batch_id: undefined,
    from_record_id: undefined,
    to_record_id: undefined,
    batch: undefined,
    recordFrom: undefined,
    recordTo: undefined,
    method: undefined,
    author: undefined,
    memo: undefined,
    is_done: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function Role() {
  return {
    id: undefined,
    name: undefined,
    display_name: undefined,
    users: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function Suggest() {
  return {
    id: undefined,
    parent_id: undefined,
    parent: undefined,
    children: undefined,
    name: undefined,
    json_data: [],
    memo: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function TestMethod() {
  return {
    id: undefined,
    name: undefined,
    file: undefined,
    content: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function TestRecord() {
  return {
    id: undefined,
    product_batch_id: undefined,
    batch: ProductBatch(),
    show_reality: undefined,
    test_times: undefined,
    conclusion: undefined,
    testers: undefined,
    is_archived: undefined,
    memo: undefined,
    items: undefined,
    willDispose: undefined,
    disposed: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function TestRecordItem() {
  return {
    id: undefined,
    test_record_id: undefined,
    testRecord: undefined,
    item: undefined,
    spec: TestSpec(),
    value: undefined,
    fake_value: undefined,
    conclusion: undefined,
    tester: undefined,
    memo: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function TestStatistics() {
  return {
    id: undefined,
    year: undefined,
    month: undefined,
    qc_type: undefined,
    tests_num: undefined,
    once_disqualification_num: undefined,
    disqualification_num: undefined,
    force_accept_num: undefined,
    category_id: undefined,
    category: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function TestWay() {
  return {
    id: undefined,
    name: undefined,
    way: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}

export function TestWayItem() {
  return {
    name: '',
    method: '',
    method_id: '',
    spec: TestSpec()
  }
}

export function TestSpec() {
  return {
    is_show: true, // 是否展示
    required: true, // 是否必须填值项
    value_type: '', // RANGE, INFO, NUMBER, ONLY_SHOW
    data: {
      min: 0,
      max: 0,
      value: '',
      memo: '',
      unit: ''
    }
  }
}

export function User() {
  return {
    id: undefined,
    name: undefined,
    email: undefined,
    avatar: undefined,
    metas: undefined,
    roles: undefined,
    created_at: undefined,
    updated_at: undefined
  }
}
