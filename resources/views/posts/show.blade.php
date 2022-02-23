@extends('layouts.app')
@section('content')
    <div class="container bg-light">
        <div class="post text-black">
            <div class="post-op d-flex">
                <div class="post-op-profile d-inline">
                    <img style="width:60px" class="rounded-circle" src="https://is3-ssl.mzstatic.com/image/thumb/Features115/v4/34/24/25/34242531-6770-091a-f7df-0b1df13e3214/mzl.vzdacmjl.jpg/375x375cc.webp" alt="">
                </div>
                <div class="post-op-info d-inline pt-1 pl-2">
                    <div class="d-block">{{ $Post->user->name }}</div>
                    <i class="fas fa-clock"></i> <span title="{{ date_format($Post->created_at, "Y-m-d h:i:sa") }}">{{ $Post->createdAtFormat() }}</span>
                </div>
            </div>
            <div class="post-content">
                {{$Post->content}}
            </div>
            <div class="post-infos row">
                <div class="col-6 text-left">
                    <i class="far fa-thumbs-up text-primary"></i> <span class="likecount">{{ $Post->Likes->count() }}</span>
                </div>
                <div class="col-6 text-right">
                    {{ $Post->Comments->count() }} {{ $Post->Comments->count() < 1 ? "Comment" : "Comments" }}
                </div>
            </div>
            <div class="post-public-controls row" style="border-bottom: 1px solid black;">
                <div onclick="likeClicked(this)" data-liked="{{ $Post->Likes()->where("user_id", auth()->user()->id)->get()->count() != 0 }}" style="cursor: pointer;" class="col-6 text-center user-select-none">
                    <h4><i class="@if($Post->Likes()->where("user_id", auth()->user()->id)->get()->count() != 0)fas @else far @endif fa-thumbs-up text-primary"></i> <span class="d-none d-md-inline">Like</span></h3>
                </div>
                <div style="cursor: pointer;" class="col-6 text-center user-select-none">
                    <h4 class=""><i class="far fa-comment"></i> <span class="d-none d-md-inline">Comment</span></h3>
                </div>
            </div>
            <div class="post-comments">
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        //configurations
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        //variables
        let likeLoading = false; //checker if likeClicked is loading.



        //functions
        const likeClicked = (likebtn) => {
            if(!likeLoading){
                likeLoading = true;
                $(likebtn).addClass("disabled");
                let liked = $(likebtn).data("liked");
                $.ajax({
                    'url': "{{ route('posts-like', ['post' => '__post__']) }}".replace("__post__", {{ $Post->id }}),
                    'method': "POST",
                    success: function(data){
                        let likecount = $(likebtn).parent().parent().find(".likecount");
                        liked = !liked;
                        $(likebtn).data("liked", liked);
                        if(liked){
                            $(likebtn).find("i").removeClass("far");
                            $(likebtn).find("i").addClass("fas");
                        }
                        else{
                            $(likebtn).find("i").removeClass("fas");
                            $(likebtn).find("i").addClass("far");
                        }
                        likecount.text(data.count);
                        likeLoading = false

                    },
                    error: function(data){
                        console.log(data);
                        likeLoading = false;
                    }
                });
            }
        }
    </script>
@endsection