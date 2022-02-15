@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Change Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route("user-changepass") }}">
                        @csrf
                        @method("PATCH")
                        @can("Reset Password Everyone")
                            <div class="row mb-3">
                                <label for="user_id" class="col-md-4 col-form-label text-md-end">{{ __('User') }}</label>

                                <div class="col-md-6">
                                    <select onchange="userChange(this)" id="user_id" class="form-control @error('user_id') is-invalid @enderror" name="user_id" required>
                                        @foreach($users as $user)
                                            <option @if(auth()->user()->id == $user->id) selected @endif value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        @endcan
                        <div class="row mb-3">
                            <label for="old_password" class="col-md-4 col-form-label text-md-end">{{ __('Current Password') }}</label>

                            <div class="col-md-6 input-group">
                                <input id="old_password" type="password" class="form-control @error('old_password') is-invalid @enderror" name="old_password" required autocomplete="old_password">
                                <div class="input-group-append">
                                    <button onclick="showPassword(this)" data-state="0" type="button" class="btn"><i class="fas fa-eye"></i></button>
                                </div>
                                @error('old_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
                            <div class="col-md-6 input-group">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                
                                <div class="input-group-append">
                                    <button onclick="showPassword(this)" data-state="0" type="button" class="btn"><i class="fas fa-eye"></i></button>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6 input-group">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                <div class="input-group-append">
                                    <button onclick="showPassword(this)" data-state="0" type="button" class="btn"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Change Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section("scripts")
<script>
    const userChange = (selectUser) => {
        if($(selectUser).val() != {{ auth()->user()->id }})
                $("label[for='old_password']").text("Enter your password");
            else
                $("label[for='old_password']").text("Current Password");
    }
    const showPassword = (btn) => {
        let state = $(btn).attr("data-state");
        if(state == 0){
            $(btn).attr("data-state", 1);
            $(btn).parent().prev().attr("type", "text");
        }
        else{
            $(btn).attr("data-state", 0);
            $(btn).parent().prev().attr("type", "password");
        }
    }

</script>
@endsection
