<template>
  <main class="page">
    <TopBar @logout="handleLogout" />

    <section class="panel">
      <h2>Reservation & QR</h2>

      <van-empty v-if="!reservation" description="No reservation" />

      <template v-else>
        <div class="key-value">
          <div>Date</div>
          <div>{{ reservation.reservation_date }}</div>
          <div>Meal</div>
          <div>{{ reservation.meal_type }}</div>
          <div>Type</div>
          <div>{{ reservation.reservation_type }}</div>
          <div>Status</div>
          <div>{{ reservation.status }}</div>
          <div>Meals</div>
          <div>{{ reservation.meal_count }}</div>
          <div>Valid Until</div>
          <div>{{ reservation.qr_expiry_time }}</div>
        </div>

        <van-cell-group inset>
          <van-field label="QR Payload" :model-value="qrContent" type="textarea" rows="4" readonly />
        </van-cell-group>

        <div class="actions-row">
          <van-button type="primary" plain @click="refreshQr">Refresh QR</van-button>
          <van-button type="warning" plain @click="openEdit">Edit</van-button>
          <van-button type="danger" plain @click="cancelReservation">Cancel</van-button>
        </div>
      </template>
    </section>

    <BottomNav />

    <van-dialog v-model:show="showEditor" title="Edit Reservation" show-cancel-button @confirm="submitEdit">
      <div class="dialog-body">
        <van-field v-model="editForm.reservation_type" label="Type" />
        <van-field v-model.number="editForm.visitor_count" label="Visitor" type="number" />
        <van-field v-model="editForm.pickup_for_staff_code" label="Pickup Staff" />
        <van-field v-model="editForm.remark" label="Remark" type="textarea" rows="2" />
      </div>
    </van-dialog>
  </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { showConfirmDialog, showFailToast, showSuccessToast } from 'vant';
import { deleteReservation, getReservation, getReservationQr, getReservations, logout, updateReservation } from '../../api/canteen';
import { setAccessToken } from '../../api/http';
import BottomNav from '../../components/BottomNav.vue';
import TopBar from '../../components/TopBar.vue';

const route = useRoute();
const router = useRouter();

const reservation = ref(null);
const qrContent = ref('');
const showEditor = ref(false);
const editForm = reactive({
  reservation_type: 'self',
  visitor_count: 0,
  pickup_for_staff_code: '',
  remark: '',
});

async function loadReservation() {
  try {
    const queryId = route.query.id;

    if (queryId) {
      const response = await getReservation(Number(queryId));
      reservation.value = response.data.data;
    } else {
      const response = await getReservations({ date_from: new Date().toISOString().slice(0, 10) });
      reservation.value = response.data.data?.data?.[0] || null;
    }

    if (reservation.value) {
      await refreshQr();
    }
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Failed to load reservation');
  }
}

async function refreshQr() {
  if (!reservation.value) {
    return;
  }

  try {
    const response = await getReservationQr(reservation.value.id);
    qrContent.value = JSON.stringify(response.data.data.payload, null, 2);
    reservation.value = response.data.data.reservation;
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Failed to load QR');
  }
}

function openEdit() {
  if (!reservation.value?.is_editable) {
    showFailToast('Reservation cannot be edited');
    return;
  }

  editForm.reservation_type = reservation.value.reservation_type;
  editForm.visitor_count = reservation.value.visitor_count;
  editForm.pickup_for_staff_code = reservation.value.pickup_for_staff_code || '';
  editForm.remark = reservation.value.remark || '';
  showEditor.value = true;
}

async function submitEdit() {
  try {
    const response = await updateReservation(reservation.value.id, editForm);
    reservation.value = response.data.data;
    await refreshQr();
    showSuccessToast('Reservation updated');
  } catch (error) {
    showFailToast(error.response?.data?.message || 'Update failed');
  }
}

async function cancelReservation() {
  if (!reservation.value) {
    return;
  }

  try {
    await showConfirmDialog({
      title: 'Confirm',
      message: 'Cancel this reservation?',
    });

    await deleteReservation(reservation.value.id);
    showSuccessToast('Reservation cancelled');
    await loadReservation();
  } catch (error) {
    if (error?.message === 'cancel') {
      return;
    }

    showFailToast(error.response?.data?.message || 'Cancel failed');
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

onMounted(loadReservation);
</script>
