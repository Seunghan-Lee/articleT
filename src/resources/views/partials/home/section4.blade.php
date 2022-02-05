{{--
  Section Name: 인기포스트
--}}

<section class="py-[28px] lg:py-[80px]">
  <div class="container mx-auto">
    <div class="sm:py-[30px]">
      <div class="text-center text-[24px] sm:text-[40px] font-bold mb-[29px pb-[14px] sm:mb-[45px]">인기 포스트</div>
      <ul class="grid gap-y-[54px] sm:gap-y-[62px]">
        @foreach ($popularityPosts as $post)
          <li class="lg:-card__s7 lg:!card__s6">
            @include('partials.card.template', [
              'title' => $post->post_title,
              'permalink' => $post->permalink,
              'thumbnail' => $post->thumbnail,
              'date' => $post->date,
              'excerpt' => $post->excerpt,
              'collection' => $post->collection,
              'hashtag' => $post->hashtag,
              'category' => $post->category,
            ])
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</section>
