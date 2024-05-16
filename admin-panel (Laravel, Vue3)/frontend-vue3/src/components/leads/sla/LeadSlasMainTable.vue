<script setup>
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import MainTableFilters from './filters/MainTableFilters.vue';
import ConfirmDialog from '@/components/global/dialogs/ConfirmDialog.vue';
import Pagination from '@/components/global/pagination/Pagination.vue';
import { useLeadSlasListStore } from '@/store/leads/sla/lead_slas_list';

const lead_slas = useLeadSlasListStore();
const router = useRouter();
const on_modal_alert_delete = ref(false);
const id_delete_lead_sla = ref(null);

const onRequest = (value) => {
  lead_slas.UPDATE_PAGINATION_FILTER(value);
  lead_slas.getLeadSlas();
};

const paginationModel = computed({
  get: () => lead_slas.pagination,
  set: (value) => {
    lead_slas.UPDATE_PAGINATION(value);
  },
});

const go_to_edit_lead_sla = (item) => {
  if (item.row.id) {
    router.push({
      name: 'edit_lead_sla',
      params: {
        id: item.row.id,
      },
    });
  }
};

const on_delete_modal = (item) => {
  if (item.row.id) {
    id_delete_lead_sla.value = item.row.id;
  }
  on_modal_alert_delete.value = true;
};

const delete_lead_sla = () => {
  if (id_delete_lead_sla.value) {
    lead_slas.deleteLeadSla(id_delete_lead_sla.value);
  }
  on_modal_alert_delete.value = false;
};

const update_pagination = (key, value) => {
  lead_slas.UPDATE_PAGINATION_KEY({ key, value });
  lead_slas.getLeadSlas();
};

// eslint-disable-next-line consistent-return
const color_chips = (item) => {
  if (item.row.color) {
    // Удаляем символ # из цвета
    const colorWithoutHash = item.row.color.slice(1);

    // Создаем новый css класс
    const style = document.createElement('style');

    style.textContent = `
      .bg-${colorWithoutHash} {
        background-color: ${item.row.color};
      }
    `;
    document.head.appendChild(style);

    return `bg-${colorWithoutHash}`;
  }
};

</script>

<template>
  <div>
    <q-table
      class="q-ma-md"
      v-model:pagination="paginationModel"
      :rows="lead_slas.leadSlasList"
      :columns="lead_slas.leadSlasListHeaders"
      :loading="lead_slas.leadSlasListLoading"
      row-key="id"
      hide-pagination
      @request="onRequest"
    >

      <template v-slot:top>
        <div>
          <MainTableFilters />
        </div>
      </template>

      <template v-slot:body-cell-color="item">
        <q-td :props="item">
          <div>
            <q-badge
              :class="color_chips(item)"
              text-color="black"
              class="status_chip"
              :label="item.value"
            />
          </div>
        </q-td>
      </template>

      <template v-slot:body-cell-actions="item">
        <q-td :props="item">
          <q-btn
            flat
            round
            color="primary"
            icon="edit"
            @click="go_to_edit_lead_sla(item)"
          />

          <q-btn
            flat
            round
            color="negative"
            icon="delete"
            @click="on_delete_modal(item)"
          />
        </q-td>
      </template>

    </q-table>

    <div class="row justify-center q-mt-md">
      <Pagination
        :pagination_props="paginationModel"
        @change_page="update_pagination('page', $event)"
        @page_size_changed="update_pagination('rowsPerPage', $event)"
      />
    </div>

    <ConfirmDialog
      v-model="on_modal_alert_delete"
      text_button="Удалить"
      title_alert="Подтвердите удаление"
      text="Вы действительно хотите удалить SLA?"
      :close_popup="true"
      @submit="delete_lead_sla"
    />
  </div>
</template>

<style scoped>
.status_chip {
  padding: 4px 12px;
  border-radius: 24px;
}

</style>
