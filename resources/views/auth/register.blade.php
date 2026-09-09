<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Blog - Register</title>

    <link href="{{asset('assets/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="{{asset('assets/css/sb-admin-2.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            <form class="user" method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full name" value="{{ old('full_name') }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Contact number" value="{{ old('mobile') }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email address" value="{{ old('email') }}" autocomplete="off">
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" autocomplete="off">
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" class="form-control"
                                            id="password-confirm" name="password_confirmation" placeholder="Confirm password">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company name" value="{{ old('company_name') }}"> 
                                        </div>
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <div class="form-group">
                                            <select class="form-control country" name="country" id="country">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <textarea id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" autocomplete="off" placeholder="Address"></textarea> 
                                    @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <span class="text-danger" id="error_address"></span>
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-user">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                                
                            </form>
                            <hr>
                            <!-- <div class="text-center">
                                <a class="small" href="forgot-password.html">Forgot Password?</a>
                            </div> -->
                            <div class="text-center">
                                <a class="small" href="{{route('login')}}">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="{{asset('assets/js/jquery.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/js/jquery.easing.min.js')}}"></script>
    <script src="{{asset('assets/js/sb-admin-2.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.full.min.js"></script>
    <script type="text/javascript">
        $('#country').select2({
           placeholder: 'Search Country',
           ajax: {
               url: "{{route('country_search')}}",
               dataType: 'json',
               delay: 250,
               processResults: function (data) {
                   return {
                       results: jQuery.map(data, function (item) {
                           return {
                               text: item.country_name,
                               id: item.country_name
                           }
                       })
                   };
               },
               cache: true
           }
       });
    </script>
</body>

</html>