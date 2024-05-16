<script setup>
import {
  onMounted, onUnmounted, computed,
} from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useLeadSlasDetailStore } from '@/store/leads/sla/lead_sla_detail';
import LeadSlaCardFields from '@/components/leads/sla/LeadSlaCardFields.vue';
import Button from '@/components/global/buttons/Button.vue';
import InfoDialog from '@/components/global/dialogs/InfoDialog.vue';

const route = useRoute();
const router = useRouter();
const lead_sla_details = useLeadSlasDetailStore();

lead_sla_details.getLeadStatuses();

onMounted(() => {
  if (route.params.id) {
    lead_sla_details.getLeadSla(route.params.id);
  }
});

onUnmounted(() => {
  lead_sla_details.SET_CLEAR_NEW_LEAD_SLA();
});

let warning_message = null;
const add_lead_sla = () => {
  if (!lead_sla_details.VALIDATE_NEW_LEAD_SLA()) {
    lead_sla_details.UPD_LEAD_SLA_VALIDATE_TOGLE(true);

    warning_message = 'Заполните обязательные поля';

    if (Number.isNaN(parseFloat(lead_sla_details.new_lead_sla.time_from))) {
      warning_message = 'Время числовое значение';
      return;
    }

    return;
  }
  warning_message = null;

  if (route.params.id) {
    lead_sla_details.editLeadSla(route.params.id);
  } else {
    lead_sla_details.addLeadSla();
  }
};

const go_back = () => {
  router.push({
    name: 'lead_slas',
  });
};

const is_lead_sla_published = computed({
  get: () => lead_sla_details.is_lead_sla_published,
});

const is_lead_sla_published_close = () => {
  lead_sla_details.UPD_LEAD_SLA_PUBLISHED_TOGLE(false);
  router.push({
    name: 'lead_slas',
  });
};

const is_lead_sla_validate = computed({
  get: () => lead_sla_details.is_modal_lead_sla_validate,
});

const is_lead_sla_validate_close = () => {
  lead_sla_details.UPD_LEAD_SLA_VALIDATE_TOGLE(false);
};

const is_error_published = computed({
  get: () => lead_sla_details.is_error_published,
});
const is_error_published_message = computed({
  get: () => lead_sla_details.is_error_published_message,
});

const is_lead_sla_error_close = () => {
  lead_sla_details.UPD_LEAD_SLA_ERROR_PUBLISHED_TOGLE(false);
};

</script>

<template>
  <div>
    <div
      v-if="route.params.id"
      class="page_header"
    >
      <span>Форма редактирования SLA по лидам
        сроком от "{{ lead_sla_details.new_lead_sla.time_from }}" часов</span>
    </div>
    <div
      v-else
      class="page_header"
    >
      <span>Добавление SLA по лидам</span>
    </div>

    <LeadSlaCardFields class="q-mx-lg" />

    <div class="row q-ma-lg">
      <Button
        text="Сохранить"
        push
        no_caps
        @click.stop="add_lead_sla"
      />
      <Button
        class="btn"
        text="Отменить"
        text_color="black"
        no_caps
        flat
        @click.stop="go_back"
      />
    </div>

    <InfoDialog
      v-model="is_lead_sla_published"
      title_alert="SLA успешно добавлен"
      :text=is_error_published_message
      @close="is_lead_sla_published_close"
    />

    <InfoDialog
      v-model="is_lead_sla_validate"
      title_alert="Внимание"
      :text=warning_message
      @close="is_lead_sla_validate_close"
    />

    <InfoDialog
      v-model="is_error_published"
      title_alert="Внимание"
      :text=is_error_published_message
      @close="is_lead_sla_error_close"
    />
  </div>
</template>

<style scoped>
.page_header {
  font-weight: normal;
  font-size: 24px;
  padding-left: 24px;
  padding-top: 16px;
}
</style>
