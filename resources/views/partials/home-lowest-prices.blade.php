{{-- ash_fix8: homepage lowest-price row. Uses existing product-card styles. --}}
@if(!empty($lowest['items']) && count($lowest['items']))
<section class="hv-section ash-lowprices" data-aos="fade-up" data-aos-delay="150">
  <div class="container">
    <div class="hv-head">
      <div>
        <h2>{{ __('Lowest prices') }}</h2>
        <p>{{ __('The most affordable products in the shop right now') }}</p>
      </div>
      <a class="hv-head-link" href="{{ URL::to('/shop') }}?sort=price_asc">{{ __('See all') }}<i class="dwg-arrow-right"></i></a>
    </div>
    <div class="ash-low-row">
      <button type="button" class="ash-low-arrow ash-low-prev" aria-label="{{ __('Previous') }}">&#8249;</button>
      <div class="ash-low-track" tabindex="0">
        @foreach($lowest['items'] as $featured)
        @php
          $price = \Fickrr\Helpers\Helper::catalog_price($featured);
          $price_exact = \Fickrr\Helpers\Helper::is_single_price_item($featured);
          $count_rating = \Fickrr\Helpers\Helper::count_rating($featured->ratings);
          $hvTag = __('Low price');
          $hvTagClass = 'hv-tag--value';
        @endphp
        @include('partials.product-card')
        @endforeach
      </div>
      <button type="button" class="ash-low-arrow ash-low-next" aria-label="{{ __('Next') }}">&#8250;</button>
    </div>
  </div>
</section>
<style>
.ash-low-row{position:relative;display:flex;align-items:stretch}
.ash-low-track{display:flex;gap:0;overflow-x:auto;-webkit-overflow-scrolling:touch;scroll-snap-type:x mandatory;scroll-behavior:smooth;padding:4px 2px 12px;scrollbar-width:thin;flex:1 1 auto}
.ash-low-track > [class*="col-"]{flex:0 0 260px;max-width:260px;margin-bottom:0;scroll-snap-align:start}
@media (max-width: 575.98px){
  .ash-low-track > [class*="col-"]{flex:0 0 78%;max-width:78%}
  .ash-low-arrow{display:none}
}
.ash-low-arrow{flex:0 0 36px;width:36px;border:1px solid #e5e7eb;background:#fff;border-radius:999px;align-self:center;height:36px;line-height:1;font-size:22px;color:#111827;cursor:pointer;z-index:1;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.ash-low-arrow:hover{background:#f8fafc}
.ash-low-prev{margin-right:8px}
.ash-low-next{margin-left:8px}
</style>
<script>
(function(){
  var row=document.querySelector('.ash-low-row');
  if(!row) return;
  var track=row.querySelector('.ash-low-track');
  var prev=row.querySelector('.ash-low-prev');
  var next=row.querySelector('.ash-low-next');
  function step(){ var c=track.querySelector('[class*="col-"]'); return c ? c.getBoundingClientRect().width + 8 : 280; }
  function go(dir){ track.scrollBy({left: dir * step(), behavior:'smooth'}); }
  if(prev) prev.addEventListener('click', function(){ go(-1); });
  if(next) next.addEventListener('click', function(){ go(1); });
  var timer=null;
  function play(){
    stop();
    if(!window.matchMedia('(min-width: 992px)').matches) return;
    timer=setInterval(function(){
      if(track.scrollLeft + track.clientWidth >= track.scrollWidth - 8){
        track.scrollTo({left:0, behavior:'smooth'});
      } else { go(1); }
    }, 4000);
  }
  function stop(){ if(timer){ clearInterval(timer); timer=null; } }
  row.addEventListener('mouseenter', stop);
  row.addEventListener('mouseleave', play);
  row.addEventListener('focusin', stop);
  row.addEventListener('focusout', play);
  play();
})();
</script>
@endif
