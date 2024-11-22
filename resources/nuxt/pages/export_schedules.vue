<template>
  <MainLayout
    :has-form="false"
    ref="layout"
    @mounted="mainLayoutMounted()"
  >
    <dialog class="ts fullscreen modal" slot="content" open>
      <div class="header">
        {{ CONSTANTS.FORM_TEXT.title }}
      </div>
      <div class="content">
        <form class="ts horizontal form">
          <div class="field">
            <label><sup class="required">*</sup>{{ CONSTANTS.FORM_TEXT.dateFromTo }}</label>
            <input
              type="date"
              v-model="filters.dateFrom"
            >
            <span class="tilde">~</span>
            <input
              type="date"
              v-model="filters.dateTo"
            >
          </div>
          <div class="field">
            <label>{{ CONSTANTS.FORM_TEXT.placeFromTo }}</label>
            <input v-model="filters.placeFrom">
            <span class="tilde">~</span>
            <input v-model="filters.placeTo">
          </div>
        </form>
      </div>
      <div class="actions" v-if="config.mode != 'view'">
        <button
          class="ts positive button"
          :class="{ loading: config.loading }"
          @click="submit"
        >
          {{ COMMON.TEXT.submit }}
        </button>
      </div>
    </dialog>
  </MainLayout>
</template>

<script>
import CONSTANTS from '../constants.js';
import MainLayout from '../layouts/main.vue';

import DataUtil from '../utils/DataUtil'

export default {
  data() {
    const today = DataUtil.formatDateInput(new Date())

    return {
      COMMON: CONSTANTS.common,
      CONSTANTS: CONSTANTS.export_schedules,
      config: {
        loading: false,
      },
      filters: {
        dateFrom: today,
        dateTo: today,
        placeFrom: "",
        placeTo: "",
      }
    };
  },
  async mounted() {
    window.mainLayout.contentLoaded();

    this.$emit("mounted");
  },
  methods: {
    mainLayoutMounted() {
    },
    submit() {
      const url = '/api/schedules/export'
      window.$nuxt.$axios.setHeader({ 'Content-Type': 'application/json' });
      window.$nuxt.$axios.post(
        url,
        this.filters,
        { responseType: 'blob' }
      ).then((response) => {
        const fileURL = window.URL.createObjectURL(new Blob([response.data]));
        const fileLink = document.createElement('a');

        fileLink.href = fileURL;
        fileLink.setAttribute('download', `${this.filters.dateFrom}~${this.filters.dateTo}.xlsx`);
        document.body.appendChild(fileLink);

        fileLink.click();
      }).catch(async (error) => {
        if (error.response.status === 500 && !DataUtil.isEmpty(window.mainLayout)) {
          window.mainLayout.showSnackbar(
            'error',
            CONSTANTS.messages['unknown-error'] + CONSTANTS.messages["contact-maintenance"]
          );
        } else if (error.response.status === 401) {
          window.mainLayout.showSnackbar(
            'error',
            this.CONSTANTS.messages.permissionDined
          );
        } else if (error.response.status === 400) {
          window.mainLayout.showSnackbar(
            'error',
            error.response.data
          );
        } else if (error.response.status === 404) {
          window.mainLayout.showSnackbar(
            'error',
            this.CONSTANTS.messages.notFound
          );
        } else {
          throw error;
        }
      });
    },
  },
  components: {
    MainLayout,
  },
}
</script>

<style>
.tilde {
  margin: 0 15px;
}
</style>

