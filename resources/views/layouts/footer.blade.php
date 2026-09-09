            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <!-- <span>Copyright &copy; Your Website 2020</span> -->
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade modal-scroll" id="modal_banner" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Crop Banner Image Before Upload</h5>
              <button type="button" class="close btns" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="img-container">
                  <div class="row">
                      <div class="col-md-10">
                          <img src="" id="banner_sample_image" class="img-responsive" style="width: 100%; height: auto;" />
                      </div>
                      <!-- <div class="col-md-4">
                          <div class="imgs_preview" style="margin-left: 10px"></div>
                      </div> -->
                  </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" id="banner_crop" class="btn btn-primary"><span class="crop_text_banner">Crop Image</span>
                <span class="crop_wait_banner" style="display: none">                      
                <i class="fa fa-circle-o-notch fa-spin"></i> Please Wait
                </span>
              </button>
              <button type="button" class="btn btn-warning" data-dismiss="modal" aria-label="Close">
                  Close
              </button>
            </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{asset('assets/js/jquery.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{asset('assets/js/jquery.easing.min.js')}}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{asset('assets/js/sb-admin-2.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
    <script src="https://unpkg.com/dropzone"></script>
<!-- Cropper.js JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script type="text/javascript">
      var pathname = window.location;
      if(pathname == "{{route('post_index')}}" || pathname == "{{route('post_create')}}") 
      {
        $("#collapseTwo").addClass('show');
        $("#collapsePostItem").removeClass('collapse');
        
      }else{
        $("#collapseTwo").removeClass('show');
        $("#collapsePostItem").addClass('collapse');
      }

      // topics

      if(pathname == "{{route('topics')}}" || pathname == "{{route('topic_assign')}}") 
      {
        $("#collapseTopic").addClass('show');
        $("#collapseTopicItem").removeClass('collapse');
        
      }else{
        $("#collapseTopic").removeClass('show');
        $("#collapseTopicItem").addClass('collapse');
      }

      // suggestions

      if(pathname == "{{route('destination_suggetion')}}" || pathname == "{{route('topic_suggetion')}}") 
      {
        $("#collapseSuggertion").addClass('show');
        $("#collapseTopicItem").removeClass('collapse');
        
      }else{
        $("#collapseSuggertion").removeClass('show');
        $("#collapseTopicItem").addClass('collapse');
      }
    </script>
    <script type="text/javascript">
      $("li a").each(function() {   
          //alert(this.href == window.location.href);
          if (this.href == window.location.href) {
              $(this).parent().addClass("active");
          }
          else{
              $(this).parent().removeClass("active");
          }
      })
  </script>
  <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js"></script>

  <script type="text/javascript">
    $( document ).ready(function() {
      // Your web app's Firebase configuration
      var firebaseConfig = {
        apiKey: "AIzaSyB3ueelOA5o9HQlWpi_DFS1yZXQoqROpkI",
        authDomain: "blog-34502.firebaseapp.com",
        projectId: "blog-34502",
        storageBucket: "blog-34502.appspot.com",
        messagingSenderId: "514286929789",
        appId: "1:514286929789:web:6c6c709a095f28d737ee14",
        measurementId: "G-5C1RPDJSXG"
      };
      // Initialize Firebase
      firebase.initializeApp(firebaseConfig);

      const messaging = firebase.messaging();

      function initFirebaseMessagingRegistration() {
          
          messaging.requestPermission().then(function () {
              console.log(messaging.getToken());
              return messaging.getToken()
          }).then(function(token) {
              $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
              });

              $.ajax({
                  url: "{{ route('fcmUpdate') }}",
                  type: 'POST',
                  data: {
                      token: token
                  },
                  dataType: 'JSON',
                  success: function (response) {
                      console.log(response)
                  },
                  error: function (err) {
                      console.log(" Token Error: " + err);
                  },
              });

          }).catch(function (err) {
              console.log(`Token Error :: ${err}`);
          });
      }

      initFirebaseMessagingRegistration();
    
      messaging.onMessage(function({data:{body,title,icon,click_action}}){
        const noteTitle = title;
        const noteOptions = {
            body: body,
            icon: icon,
        };
        const notif = new Notification(noteTitle, noteOptions);

        //const notif =  new Notification(title, {body,icon}, {click_action});
          notif.onclick = function() {
             // window.location=click_action;
              window.open(click_action,'_blank');
          };
      });
    });
  </script>

  <script type="text/javascript">
    function showNotification(id){
      //alert(id);
      jQuery.ajax({
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          method: 'POST',
          url: "/notification-status-change/"+id,
          contentType: false,
          processData: false,
          success: function (data) {
              //alert(data.url);
             window.location = data.url;
          },
          errors: function () {
            
          }
      });
    }
  </script>
  @section('footer')

  @show