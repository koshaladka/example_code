<template>
  <v-dialog :value="value" width="auto" :class="xs ? 'w-100' : ''">
    <v-card class="text-center" :class="xs ? 'px-4 py-8' : 'px-16 py-10'">
      <v-row no-gutters>
        <v-col>
          <h2>{{ text }}</h2>

          <v-row no-gutters class="mt-8 mx-4">
            <v-col class="">
              <v-btn
                class="w-100"
                color="red"
                :block="xs"
                size="large"
                @pointerdown="closePopup(true)"
              >
                Понятно
              </v-btn>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-card>
  </v-dialog>
</template>

<script lang="ts" setup>
import { useDisplay } from "vuetify";

const { xs } = useDisplay();

defineProps({
  value: {
    type: Boolean,
    default: false,
  },
  text: {
    type: String,
    default: "ККМ в работе.",
  },
});

const emit = defineEmits(["close"]);

function closePopup(val: boolean) {
  emit("close", val);
}
</script>

<style lang="sass" scoped></style>
