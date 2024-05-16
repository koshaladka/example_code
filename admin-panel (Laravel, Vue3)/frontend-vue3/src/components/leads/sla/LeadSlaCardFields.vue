<script setup>
import { computed } from 'vue';
import { useLeadSlasDetailStore } from '@/store/leads/sla/lead_sla_detail';
import InputSkeleton from '@/components/global/skeleton/InputSkeleton.vue';
import Select from '@/components/global/inputs/select/Select.vue';

const lead_slas_detail_store = useLeadSlasDetailStore();

const inputTimeFromModel = computed({
  get: () => lead_slas_detail_store.new_lead_sla.time_from,
  set: (value) => {
    lead_slas_detail_store.UPD_NEW_LEAD_SLA_DETAIL({ key: 'time_from', value });
  },
});

const leadStatusModel = computed({
  get: () => lead_slas_detail_store.new_lead_sla.lead_status_slug,
  set: (value) => {
    lead_slas_detail_store.UPD_NEW_LEAD_SLA_DETAIL({ key: 'lead_status_slug', value });
  },
});

const colorModel = computed({
  get: () => lead_slas_detail_store.new_lead_sla.color,
  set: (value) => {
    lead_slas_detail_store.UPD_NEW_LEAD_SLA_DETAIL({ key: 'color', value });
  },
});

</script>

<template>
  <div>
    <div class="q-my-lg">
      <span class="title">Карточка SLA по лидам</span>
    </div>
    <div class="row q-gutter-x-md q-mb-sm">
      <div class="col-12 col-sm-9 col-md-6 col-lg-4">
        <InputSkeleton
          :loading="lead_slas_detail_store.loading"
          :dense="true"
        >
          <q-input
            v-model="inputTimeFromModel"
            outlined
            no-error-icon
            dense
            clearable
            clear-icon="clear"
            label="Введите кол-во часов *"
            :rules="[val => !!val || 'Поле обязательно для заполнения']"
          />
        </InputSkeleton>
      </div>
    </div>

    <div class="row q-gutter-x-md">
      <div class="col-12 col-sm-9 col-md-6 col-lg-4">
        <InputSkeleton
          :loading="lead_slas_detail_store.loading"
          :dense="true"
        >
          <Select
            v-model="leadStatusModel"
            :options="lead_slas_detail_store.lead_statuses"
            label="Статус лида *"
            dense
            outlined
            no-error-icon
            emit-value
            option-value="slug"
            option-label="name"
            options_type="'object'"
            :rules="[
              val => !!val || 'Поле обязательно для заполнения',
            ]"
          />
        </InputSkeleton>
      </div>
    </div>

    <div class="row q-gutter-x-md q-mb-sm">
      <div class="col-12 col-sm-9 col-md-6 col-lg-4">
        <InputSkeleton
          :loading="lead_slas_detail_store.loading"
          :dense="true"
        >
          <q-input
            v-model="colorModel"
            filled
            outlined
            no-error-icon
            dense
            clearable
            clear-icon="clear"
            label="Цвет строки в ЛКП *"
            :rules="[val => !!val || 'Поле обязательно для заполнения']"
          >
            <template v-slot:prepend>
              <q-avatar :style="`background-color: ${colorModel}`" />
            </template>
            <template v-slot:append>
              <q-icon name="colorize" class="cursor-pointer">
                <q-popup-proxy transition-show="scale" transition-hide="scale">
                  <q-color no-header-tabs v-model="colorModel" />
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
        </InputSkeleton>
      </div>
    </div>
  </div>
</template>

<style scoped>
.title {
  font-size: 18px;
  font-weight: 600;
  line-height: 24px;
}

</style>
