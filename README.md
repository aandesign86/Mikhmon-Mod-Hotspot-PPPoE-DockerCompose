# Mikhmon-Mod-Hotspot-PPPoE-DockerCompose
Source code Modifikasi Mikhmon untuk Hotspot dan PPPOE
- Bisa install di Docker Compose CasaOS
- Sudah Fix Bug Add,Remove,Edit di menu PPPOEnya
- file sumber dari https://github.com/Dailylepedia-Grup/MIKHMON-HOTSPOT-PPPOE, yang saya perbaiki pada sistem PPPOEnya

# Tampilan Dasboard
![This is an image](/ss.png)

# Cara Install
- klik tanda "+" di sebelah kanan aplikasi Casa OS
- klik Install Aplikasi Costum
- klik ikon Impor di sebelah kiri ikon (X)/Close pojok kanan Casa OS
- lalu pilih Docker Compose
- kemudian pastekan source code di bawah ini:

```yaml
name: mikhmon-hotspot-pppoe
services:
  mikhmon:
    cpu_shares: 90
    command: []
    container_name: mikhmon-app
    deploy:
      resources:
        limits:
          memory: 256M
    hostname: mikhmon-app
    image: aandesign86/mikhmon-pppoe:latest
    labels:
      icon: https://cdn.aptoide.com/imgs/e/6/1/e611d4ce74336ad6ea2b9a0ce27b4bb4_icon.png
    ports:
      - target: 80
        published: "8080"
        protocol: tcp
    restart: unless-stopped
    volumes:
      - type: bind
        source: /DATA/AppData/mikhmon
        target: /var/www/mikhmon
    devices: []
    cap_add: []
    environment: []
    network_mode: bridge
    privileged: false
x-casaos:
  author: self
  category: self
  hostname: mikhmon.aandesign86.my.id
  icon: https://cdn.aptoide.com/imgs/e/6/1/e611d4ce74336ad6ea2b9a0ce27b4bb4_icon.png
  index: /
  is_uncontrolled: false
  port_map: "8080"
  scheme: http
  store_app_id: mikhmon-hotspot-pppoe
  title:
    custom: Mikhmon V3.20
    en_us: mikhmon-pppoe


- kemudian klik Kirim
- tunggu prosess instalasi selesai
- dan mikmon bisa dijalankan

#traktir Cofee / ke DANA seiklasnya bagi yang mau berbagi
083120037386

jika kebingungan chat wa : 
https://wa.me/6283120037386
