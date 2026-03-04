<template>
  <van-tabbar route>
    <van-tabbar-item to="/admin/dashboard" icon="bar-chart-o">{{ $t('nav.admin_dashboard') }}</van-tabbar-item>
    <van-tabbar-item to="/admin/menu" icon="todo-list-o">{{ $t('nav.admin_menu') }}</van-tabbar-item>
    <van-tabbar-item to="/admin/scan" icon="scan">{{ $t('nav.admin_scan') }}</van-tabbar-item>
    <van-tabbar-item to="/admin/users" icon="friends-o">{{ $t('nav.admin_users') }}</van-tabbar-item>
    <van-tabbar-item to="/admin/reports" icon="description">{{ $t('nav.admin_reports') }}</van-tabbar-item>
  </van-tabbar>
</template>
