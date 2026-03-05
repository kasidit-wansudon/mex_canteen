<template>
  <main class="page auth-page">
    <TopBar :show-logout="false" />
    <section class="panel">
      <h2>今日 Today</h2>
      <h3>{{ today }}</h3>
      <van-form @submit="submit">
      </van-form>
      <div v-if="qrStatus === 'pass'" class="result-box result-pass">
        <van-icon name="passed" size="56" />
        <p class="result-title">通过 Pass</p>
        <p class="result-sub">可就餐：{{ portions }}份 Order: {{ portions }}</p>
        <van-row :gutter="[12, 0]" style="margin: 24px 16px 0;" justify="end">
          <van-button type="primary" style="margin-top: 8px;" @click="qrStatus = null">
            Next
          </van-button>
        </van-row>
      </div>

      <!-- QR Result: Failed -->
      <div v-else-if="qrStatus === 'fail'" class="result-box result-fail">
        <van-icon name="close" size="56" />
        <p class="result-title">不通过 Failed</p>
        <p class="result-sub">二维码过期 QR code expired</p>
        <van-row :gutter="[12, 0]" style="margin: 24px 16px 0;" justify="end">
          <van-button type="primary" style="margin-top: 8px;" @click="qrStatus = null">
            Next
          </van-button>
        </van-row>
      </div>

      <van-row :gutter="[12, 0]" style="margin: 24px 16px 0;">
        <van-col span="8">
          <van-button size="small" type="primary" block @click="qrStatus = 'pass'; portions = 3">✓ Pass</van-button>
        </van-col>
        <van-col span="8">
          <van-button size="small" type="danger" block @click="qrStatus = 'fail'">✗ Fail</van-button>
        </van-col>
        <van-col span="8">
          <van-button size="small" plain block @click="qrStatus = null">Reset</van-button>
        </van-col>
      </van-row>
    </section>
  </main>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { showFailToast, showSuccessToast } from 'vant';
import { login } from '../../api/canteen';
import { setAccessToken } from '../../api/http';
import TopBar from '../../components/TopBar.vue';

const router = useRouter();
const loading = ref(false);

const qrStatus = ref(null);
const portions = ref(0); // populated from QR response

const today = new Date().toISOString().split('T')[0];
</script>