<template>
  <main class="page">
    <TopBar @logout="handleLogout" />

    <section class="panel">
      <h2>User Management</h2>

      <div class="filter-grid">
        <van-field v-model="filters.staff_code" label="Staff Code" />
        <van-field v-model="filters.staff_name" label="Staff Name" />
        <van-field v-model="filters.email" label="Email" />
      </div>

      <van-button block type="primary" @click="loadUsers">Search</van-button>

      <van-cell-group inset>
        <van-cell
          v-for="item in users"
          :key="item.id"
          :title="`${item.staff_code} - ${item.staff_name}`"
          :label="`${item.email} / ${item.staff_type}`"
        >
          <template #value>
            <van-switch :model-value="item.account_status" @change="toggleStatus(item, $event)" />
          </template>
        </van-cell>
      </van-cell-group>

      <h3>Create Visitor</h3>
      <van-form @submit="createVisitor">
        <van-field v-model="visitor.visitor_name" label="Name" required />
        <van-field v-model="visitor.email" label="Email" />
        <van-field v-model="visitor.valid_from" type="date" label="Valid From" required />
        <van-field v-model="visitor.valid_until" type="date" label="Valid Until" required />
        <div class="form-action">
          <van-button block native-type="submit" type="success">Create Visitor</van-button>
        </div>
      </van-form>
    </section>

    <AdminNav />
  </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { showFailToast, showSuccessToast } from 'vant';
import { adminCreateVisitor, adminUpdateUserStatus, adminUsers, logout } from '../../api/canteen';
import { setAccessToken } from '../../api/http';
import AdminNav from '../../components/AdminNav.vue';
import TopBar from '../../components/TopBar.vue';

const router = useRouter();
const users = ref([]);
const filters = reactive({
  staff_code: '',
  staff_name: '',
  email: '',
});

const visitor = reactive({
  visitor_name: '',
  email: '',
  valid_from: new Date().toISOString().slice(0, 10),
  valid_until: new Date(Date.now() + 7 * 86400000).toISOString().slice(0, 10),
  account_status: true,
});

async function loadUsers() {
  try {
    const response = await adminUsers(filters);
    users.value = response.data.data?.data || [];
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Load users failed');
  }
}

async function toggleStatus(item, accountStatus) {
  try {
    await adminUpdateUserStatus(item.id, { account_status: accountStatus });
    item.account_status = accountStatus;
    showSuccessToast('Status updated');
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Update failed');
  }
}

async function createVisitor() {
  try {
    const response = await adminCreateVisitor(visitor);
    showSuccessToast(`${response.data.data.visitor_code} created`);
    visitor.visitor_name = '';
    visitor.email = '';
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Create visitor failed');
  }
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

onMounted(loadUsers);
</script>
