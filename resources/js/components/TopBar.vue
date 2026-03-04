<template>
  <header class="top-bar">
    <div class="title">{{ $t('app.title') }}</div>
    <div class="actions">
      <van-dropdown-menu active-color="#0f766e">
        <van-dropdown-item v-model="locale" :options="languageOptions" @change="changeLocale" />
      </van-dropdown-menu>
      <van-button v-if="showLogout" size="small" type="primary" @click="$emit('logout')">
        {{ $t('app.logout') }}
      </van-button>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue';
import { switchLocale } from '../i18n';

defineProps({
  showLogout: {
    type: Boolean,
    default: true,
  },
});

defineEmits(['logout']);

const locale = ref(localStorage.getItem('canteen_locale') || 'zh-CN');

const languageOptions = [
  { text: '中文', value: 'zh-CN' },
  { text: 'English', value: 'en' },
];

function changeLocale(value) {
  switchLocale(value);
}
</script>
