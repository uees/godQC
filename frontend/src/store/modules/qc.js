// import { qcMethodApi } from '@/api/qc'
import { ProductDispose } from '@/defines/models'

const fqcDisposeForm = () => {
  return {
    action: 'create',
    formData: ProductDispose(),
    visable: false
  }
}

const state = {
  fqcDisposeForm: fqcDisposeForm()
}

const mutations = {
  SET_FQC_DISPOSE_FORM: (state, fqcDisposeForm) => {
    state.fqcDisposeForm = fqcDisposeForm
  },
  UPDATE_FQC_DISPOSE_FORM: (state, fqcDisposeForm) => {
    state.fqcDisposeForm = Object.assign({}, state.fqcDisposeForm, fqcDisposeForm)
  }
}

const actions = {
  async resetFormDialog({ commit }) {
    commit('SET_FORM_DIALOG', fqcDisposeForm())
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
