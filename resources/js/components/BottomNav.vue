<template>
  <van-tabbar route>
    <van-tabbar-item to="/" icon="shop-o">{{ $t('nav.home') }}</van-tabbar-item>
    <van-tabbar-item to="/reservation" icon="qr">{{ $t('nav.reservation') }}</van-tabbar-item>
    <van-tabbar-item to="/history" icon="records">{{ $t('nav.history') }}</van-tabbar-item>
  </van-tabbar>
</template>
