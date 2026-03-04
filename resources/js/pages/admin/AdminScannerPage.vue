<template>
  <main class="page">
    <TopBar @logout="handleLogout" />

    <section class="panel">
      <h2>{{ $t('admin.scan_title') }}</h2>

      <van-field v-model="token" :label="$t('admin.qr_token')" placeholder="Paste QR token" />
      <van-button block type="primary" :loading="loading" @click="validate">Validate</van-button>

      <div v-if="result" class="scan-result" :class="result.result">
        <strong>{{ result.result.toUpperCase() }}</strong>
        <p>{{ result.message }}</p>
        <small v-if="result.data">Meals: {{ result.data.meal_count }}</small>
      </div>

      <van-button block plain type="default" @click="nextScan">{{ $t('admin.next') }}</van-button>
    </section>

    <AdminNav />
  </main>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { showFailToast } from 'vant';
import { adminValidateQr, logout } from '../../api/canteen';
import { setAccessToken } from '../../api/http';
import AdminNav from '../../components/AdminNav.vue';
import TopBar from '../../components/TopBar.vue';

const router = useRouter();
const loading = ref(false);
const token = ref('');
const result = ref(null);

async function validate() {
  if (!token.value) {
    showFailToast('QR token is required');
    return;
  }

  loading.value = true;

  try {
    const response = await adminValidateQr({ qr_code_token: token.value });
    result.value = response.data;
  } catch (error) {
    result.value = error.response?.data || { result: 'failed', message: 'Validation failed' };
  } finally {
    loading.value = false;
  }
}

function nextScan() {
  token.value = '';
  result.value = null;
}

async function handleLogout() {
  try {
    await logout();
  } catch (error) {
    // Ignore logout API error and clear token locally.
  } finally {
    setAccessToken(null);
    localStorage.removeItem('canteen_auth');
    router.push('/admin');
  }
}
</script>
