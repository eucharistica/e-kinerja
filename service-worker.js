var staticCacheName = 'NPiS';
var filesToCache = [
  '/',
  'libs/bootstrap/css/bootstrap.min.css',
  'libs/font-awesome/css/font-awesome.min.css',
  'libs/Ionicons/css/ionicons.min.css',
  'libs/datatables.net-bs/css/dataTables.bootstrap.min.css',
  'libs/datatables.net-bs/Buttons-1.5.1/css/buttons.dataTables.min.css',
  'libs/bootstrap-slider/slider.css',
  'libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css',
  'libs/bootstrap-daterangepicker/daterangepicker.css',
  'libs/clockpicker/bootstrap-clockpicker.min.css',
  'libs/iCheck/all.css',
  'libs/fullcalendar/dist/fullcalendar.min.css',
  'libs/fullcalendar/dist/fullcalendar.print.min.css',
  'libs/dist/css/skins/_all-skins.min.css',
  'libs/select2/css/select2.min.css',
  'libs/dist/css/AdminLTE.min.css',
  'libs/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css',
  'libs/jquery/jquery.min.js',
  'libs/jquery-ui/jquery-ui.min.js',
  'libs/bootstrap/js/bootstrap.min.js',
  'libs/select2/js/select2.full.min.js',
  'libs/input-mask/jquery.inputmask.js',
  'libs/input-mask/jquery.inputmask.date.extensions.js',
  'libs/input-mask/jquery.inputmask.extensions.js',
  'libs/moment/min/moment.min.js',
  'libs/bootstrap-daterangepicker/daterangepicker.js',
  'libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js',
  'libs/clockpicker/bootstrap-clockpicker.min.js',
  'libs/iCheck/icheck.min.js',
  'libs/fastclick/lib/fastclick.js',
  'libs/datatables.net-bs/js/jquery.dataTables.min.js',
  'libs/datatables.net-bs/js/dataTables.bootstrap.min.js',
  'libs/datatables.net-bs/JSZip-2.5.0/jszip.min.js',
  'libs/datatables.net-bs/pdfmake-0.1.32/pdfmake.min.js',
  'libs/datatables.net-bs/pdfmake-0.1.32/vfs_fonts.js',
  'libs/datatables.net-bs/AutoFill-2.2.2/js/dataTables.autoFill.min.js',
  'libs/datatables.net-bs/Buttons-1.5.1/js/dataTables.buttons.min.js',
  'libs/datatables.net-bs/Buttons-1.5.1/js/buttons.flash.min.js',
  'libs/datatables.net-bs/Buttons-1.5.1/js/buttons.html5.min.js',
  'libs/datatables.net-bs/Buttons-1.5.1/js/buttons.print.min.js',
  'libs/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js',
  'libs/dist/js/adminlte.min.js',
  'libs/bootstrap-slider/bootstrap-slider.js',
  'libs/fullcalendar/dist/fullcalendar.min.js',
  'libs/hightChart/highcharts.js" type="text/javascript',
  'libs/hightChart/exporting.js',
  'main.js'
];

// Start the service worker and cache all of the app's shell content
self.addEventListener('install', function (e) {
  e.waitUntil(
    caches.open(staticCacheName).then(function (cache) {
      return cache.addAll(filesToCache);
    })
  );
});

// Check if server worker is activated
self.addEventListener('activate', function (e) {
  console.log('Service worker has been activate.');
  // Delete old static cache
  e.waitUntil(
    caches.keys().then(cacheNames => {
      console.log(cacheNames);
      return Promise.all(cacheNames
        .filter(cacheName => cacheName !== staticCacheName)
        .map(cacheName => caches.delete(cacheName))
      );
    })
  );
});

// Serve cached content when offline
self.addEventListener('fetch', function (e) {
  e.respondWith(
    caches.match(e.request).then(function (response) {
      return response || fetch(e.request);
    })
  );
});
