<script setup>
import { computed } from 'vue';
import Select from '@/components/global/inputs/select/Select.vue';
import { useLeadSlasListStore } from '@/store/leads/sla/lead_slas_list';

const lead_slas = useLeadSlasListStore();

const filterStatusModel = computed({
  get: () => lead_slas.filters.leadSlasListFilterStatus,
  set: (value) => {
    lead_slas.UPDATE_FILTERS({ key: 'leadSlasListFilterStatus', value });
  },
});

const filterColorModel = computed({
  get: () => lead_slas.filters.leadSlasListFilterColor,
  set: (value) => {
    lead_slas.UPDATE_FILTERS({ key: 'leadSlasListFilterColor', value });
  },
});

const statuses = computed(() => lead_slas.statuses);
const colors = computed(() => lead_slas.colors);

const filter_update = () => {
  lead_slas.getLeadSlas();
};

</script>

<template>
  <div>
    <div class="row q-gutter-md">
      <div>
        <Select
          v-model="filterStatusModel"
          v-bind="$attrs"
          :options="statuses"
          label="Статус лида"
          style="width: 300px"
          dense
          outlined
          clearable
          multiple
          emit-value
          options_type="'object'"
          :checkboxes="true"
          :option-label="opt => Object(opt) === opt && 'name' in opt ? opt.name : null"
          :option-value="opt => Object(opt) === opt && 'slug' in opt ? opt.slug : null"
          @update:model-value="filter_update"
        />
      </div>
      <div>
        <Select
          v-model="filterColorModel"
          v-bind="$attrs"
          :options="colors"
          label="Цвет строки в ЛКП"
          style="width: 300px"
          dense
          outlined
          clearable
          multiple
          emit-value
          options_type="'object'"
          :checkboxes="true"
          :avatar="true"
          :option-label="opt => Object(opt) === opt && 'name' in opt ? opt.name : null"
          :option-value="opt => Object(opt) === opt && 'name' in opt ? opt.name : null"
          @update:model-value="filter_update"
        />
      </div>
    </div>

  </div>
</template>

<style scoped>

</style>
