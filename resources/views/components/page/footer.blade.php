<footer class="footer">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">©
        <script>
          document.write(new Date().getFullYear())
        </script> <b>Tncc - Mbeya</b>
      </div>
    </div>
  </div>
</footer>
</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->



<!-- JAVASCRIPT -->
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
</script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
<!-- pace js -->
{{-- <script src="{{ asset('assets/libs/pace-js/pace.min.js') }}"></script> --}}
<!-- flatpickr js -->
<script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>


<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/invoices-list.init.js') }}"></script>


<!-- init js -->
<script src="{{ asset('assets/js/pages/datatable-pages.init.js') }}"></script>
<script>
  if (window.feather) {
    window.feather.replace();
  }
  window.flagAssetBaseUrl = "{{ asset('assets/images/flags') }}/";
</script>
<!-- Sweet Alerts js -->
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="{{ asset('assets/js/app.js') }}"></script>

@stack('scripts')

</body>

</html>
