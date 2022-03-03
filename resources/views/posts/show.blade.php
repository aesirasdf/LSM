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
                    {{ $Post->Comments->count() }} {{ $Post->Comments->count() <= 1 ? "Comment" : "Comments" }}
                </div>
            </div>
            <div class="post-public-controls row py-2" style="border-bottom: 1px solid black; border-top: 1px solid black;">
                <div onclick="likeClicked(this)" data-liked="{{ $Post->Likes()->where("user_id", auth()->user()->id)->get()->count() != 0 }}" style="cursor: pointer;" class="col-6 text-center user-select-none">
                    <h4><i class="@if($Post->Likes()->where("user_id", auth()->user()->id)->get()->count() != 0)fas @else far @endif fa-thumbs-up text-primary"></i> <span class="d-none d-md-inline">Like</span></h3>
                </div>
                <div onclick="postWriteCommentToggle(this)" style="cursor: pointer;" class="col-6 text-center user-select-none">
                    <h4 class=""><i class="far fa-comment"></i> <span class="d-none d-md-inline">Comment</span></h3>
                </div>
            </div>
            <div class="post-comments my-3">
                <div class="post-write-comment mb-3 pb-3" style="display:none">
                    <form class="post-write-comment-form" action="{{ route("posts-comment", ["Post" => $Post->id]) }}" method="post">
                        @csrf
                        <div class="input-group">
                            <input type="text" required name="content" class="form-control" placeholder="Write a comment here...">
                            <div class="input-group-append">
                                <input type="submit" class="btn btn-primary" value="Comment">
                            </div>
                        </div>
                    </form>
                </div>
                <div>
                    @foreach($Post->Comments->sortByDesc(function($product, $key){
                        return $product["created_at"];
                    })->all() as $Comment)
                        <div class="comment py-3">
                            <div class="comment-contents d-flex">
                                <div class="commenter-pic">
                                    <img style="width:40px" class="rounded-circle" src="https://is3-ssl.mzstatic.com/image/thumb/Features115/v4/34/24/25/34242531-6770-091a-f7df-0b1df13e3214/mzl.vzdacmjl.jpg/375x375cc.webp" alt="">
                                </div>
                                <div class="comment-bubble ml-2">
                                    <div class="commenter">
                                        <div class="commenter-name font-14 text-bold">
                                            {{ $Comment->User->name }}
                                        </div>
                                    </div>
                                    <div class="comment-content font-16">
                                        {{ $Comment->content }}
                                    </div>
                                </div>
                            </div>
                            <div class="comment-public-controls d-flex pl-4">
                                <div class="comment-like text-bold">
                                    Like
                                </div>
                                <div class="comment-reply text-bold ml-4">
                                    Reply
                                </div>
                                <div class="comment-date ml-4">
                                    {{ $Comment->createdAtFormat2() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
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
                $.ajax({
                    'url': "{{ route('posts-like', ['Post' => '__post__']) }}".replace("__post__", {{ $Post->id }}),
                    'method': "POST",
                    success: function(data){
                        let likecount = $(likebtn).parent().parent().find(".likecount");
                        $(likebtn).find("i").toggleClass("fas");
                        $(likebtn).find("i").toggleClass("far");

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

        const postWriteCommentToggle = (btn) => {
            $(btn).parent().next().find(".post-write-comment").toggle();
        }

        let commentSubmitIsLoading = false;
        const commentSubmit = (form) => {
            if(!commentSubmitIsLoading){
                commentSubmitIsLoading = true;
                $.ajax({
                    "url": $(form).attr("action"),
                    "data": $(form).serialize(),
                    "method": "POST",
                    success: function(data){
                        commentSubmitIsLoading = false;
                        $(form).find("[name='content']").val("");
                        $(form).parent().next().prepend(
                        `<div class="comment py-3">
                            <div class="comment-contents d-flex">
                                <div class="commenter-pic">
                                    <img style="width:40px" class="rounded-circle" src="https://is3-ssl.mzstatic.com/image/thumb/Features115/v4/34/24/25/34242531-6770-091a-f7df-0b1df13e3214/mzl.vzdacmjl.jpg/375x375cc.webp" alt="">
                                </div>
                                <div class="comment-bubble ml-2">
                                    <div class="commenter">
                                        <div class="commenter-name font-14 text-bold">
                                            ${data.post.user.name}
                                        </div>
                                    </div>
                                    <div class="comment-content font-16">
                                        ${data.post.content}
                                    </div>
                                </div>
                            </div>
                            <div class="comment-public-controls d-flex pl-4">
                                <div class="comment-like text-bold">
                                    Like
                                </div>
                                <div class="comment-reply text-bold ml-4">
                                    Reply
                                </div>
                                <div class="comment-date ml-4">
                                    Just now
                                </div>
                            </div>
                        </div>`);
                    },
                    error: function(data){
                        commentSubmitIsLoading = false;
                        console.log(data);
                    }
                });
            }
        }



        $(document).ready(function(){
            $(".post-write-comment-form").submit(function(e){
                commentSubmit(this);
                return false;
            });
        });
    </script>
@endsection