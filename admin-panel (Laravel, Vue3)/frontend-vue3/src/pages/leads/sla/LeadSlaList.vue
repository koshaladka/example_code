<script setup>
import { useRouter } from 'vue-router';
import { useLeadSlasListStore } from '@/store/leads/sla/lead_slas_list';
import LeadSlasMainTable from '@/components/leads/sla/LeadSlasMainTable.vue';
import Button from '@/components/global/buttons/Button.vue';

const lead_slas = useLeadSlasListStore();
const router = useRouter();

lead_slas.getLeadSlas();

const go_to = () => {
  router.push({
    name: 'new_lead_sla',
  });
};
</script>

<template>
  <div>
    <div class="container page_header q-mb-lg">
      <span>Настройки SLA по лидам</span>
      <Button
        text="Создать SLA"
        push
        no_caps
        @click="go_to"
      />
    </div>
    <LeadSlasMainTable />
  </div>
</template>

<style scoped>
.page_header {
  display: flex;
  justify-content: space-between;
  font-weight: normal;
  font-size: 24px;
}
</style>
