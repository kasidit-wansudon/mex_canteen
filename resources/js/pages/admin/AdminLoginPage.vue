<template>
  <main class="page auth-page">
    <TopBar :show-logout="false" />
    <section class="panel">
      <h2>{{ $t('admin.login_title') }}</h2>
      <van-form @submit="submit">
        <van-field v-model="form.staff_code" label="Staff Code" placeholder="A0001" required />
        <van-field v-model="form.password" label="Password" type="password" required />
        <div class="form-action">
          <van-button block native-type="submit" type="primary" :loading="loading">Login</van-button>
        </div>
      </van-form>
      <p class="hint">Demo: kk / kljk</p>
    </section>
  </main>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { showFailToast } from 'vant';
import { login } from '../../api/canteen';
import { setAccessToken } from '../../api/http';
import TopBar from '../../components/TopBar.vue';

const router = useRouter();
const loading = ref(false);
const form = reactive({
  staff_code: '',
  password: '',
  device_name: 'canteen-web',
});

async function submit() {
  loading.value = true;
  try {
    const response = await login(form);
    const { access_token: token, user } = response.data;

    setAccessToken(token);
    localStorage.setItem(
      'canteen_auth',
      JSON.stringify({ token, role: user.role, staff_code: user.staff_code, staff_name: user.staff_name }),
    );

    if (user.role === 'admin') {
      router.push('/admin/dashboard');
      return;
    }

    router.push('/');
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Login failed');
  } finally {
    loading.value = false;
  }
}
</script>
