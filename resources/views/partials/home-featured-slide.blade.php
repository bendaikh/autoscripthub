{{-- ash_fix9: homepage Featured Products as a looping horizontal slider (4 items). Does not affect /featured-items. --}}
@if(!empty($featured['items']) && count($featured['items']) != 0)
<section class="hv-section hv-section--soft" data-aos="fade-up" data-aos-delay="200">
  <div class="container">
    <div class="hv-head">
      <div>
        <h2>{{ __('Featured Products') }}</h2>
        <p>{{ __('Hand-picked tools our team recommends') }}</p>
      </div>
      <a class="hv-head-link" href="{{ URL::to('/') }}/featured-items">{{ __('View All Products') }}<i class="dwg-arrow-right"></i></a>
    </div>
    <style>
    .ash-feat-row{position:relative;display:flex;align-items:stretch}
    .ash-feat-track{display:flex;gap:0;overflow-x:auto;-webkit-overflow-scrolling:touch;scroll-snap-type:x mandatory;scroll-behavior:smooth;padding:4px 2px 12px;scrollbar-width:thin;flex:1 1 auto}
    .ash-feat-track > [class*="col-"]{flex:0 0 31%;max-width:31%;margin-bottom:0;scroll-snap-align:start}
    @media (max-width: 991.98px){
      .ash-feat-track > [class*="col-"]{flex:0 0 48%;max-width:48%}
    }
    @media (max-width: 575.98px){
      .ash-feat-track > [class*="col-"]{flex:0 0 78%;max-width:78%}
      .ash-feat-arrow{display:none}
    }
    .ash-feat-arrow{flex:0 0 36px;width:36px;border:1px solid #e5e7eb;background:#fff;border-radius:999px;align-self:center;height:36px;line-height:1;font-size:22px;color:#111827;cursor:pointer;z-index:1;box-shadow:0 1px 3px rgba(0,0,0,.06)}
    .ash-feat-arrow:hover{background:#f8fafc}
    .ash-feat-prev{margin-right:8px}
    .ash-feat-next{margin-left:8px}
    </style>
    <div class="ash-feat-row">
      <button type="button" class="ash-feat-arrow ash-feat-prev" aria-label="{{ __('Previous') }}">&#8249;</button>
      <div class="ash-feat-track">
        @foreach($featured['items'] as $index => $featured)
        @php
          $price = Helper::catalog_price($featured);
          $price_exact = Helper::is_single_price_item($featured);
          $count_rating = Helper::count_rating($featured->ratings);
          $hvTag = $hvTagClass = null;
          if($index === 0)                    { $hvTag = __('Top Seller'); $hvTagClass = 'hv-tag--seller'; }
          elseif($featured->item_flash == 1)  { $hvTag = __('Trending');   $hvTagClass = 'hv-tag--trending'; }
          elseif($featured->free_download==1) { $hvTag = __('Free');       $hvTagClass = 'hv-tag--free'; }
          elseif($index === 1)                { $hvTag = __('Best Value'); $hvTagClass = 'hv-tag--value'; }
        @endphp
        @include('partials.product-card')
        @endforeach
      </div>
      <button type="button" class="ash-feat-arrow ash-feat-next" aria-label="{{ __('Next') }}">&#8250;</button>
    </div>
  </div>
</section>
<script>
(function(){
  var row=document.querySelector('.ash-feat-row');
  if(!row) return;
  var track=row.querySelector('.ash-feat-track');
  var prev=row.querySelector('.ash-feat-prev');
  var next=row.querySelector('.ash-feat-next');
  var originals=[].slice.call(track.children);
  var n=originals.length;
  if(!n) return;
  originals.forEach(function(c){ track.appendChild(c.cloneNode(true)); });
  function cardW(){ var c=track.querySelector('[class*="col-"]'); return c ? c.getBoundingClientRect().width : 300; }
  function setLeft(left, smooth){
    track.style.scrollBehavior = smooth ? 'smooth' : 'auto';
    track.scrollLeft = left;
  }
  function cycleWidth(){ return cardW() * n; }
  function normalize(){
    var w=cycleWidth();
    if(w<=1) return;
    if(track.scrollLeft >= w - 1){ setLeft(track.scrollLeft - w, false); }
    else if(track.scrollLeft < 0){ setLeft(track.scrollLeft + w, false); }
  }
  function go(dir){
    var w=cycleWidth();
    if(w<=1) return;
    if(dir>0 && track.scrollLeft + cardW() >= w - 1){
      setLeft(track.scrollLeft - w, false);
    } else if(dir<0 && track.scrollLeft <= 1){
      setLeft(track.scrollLeft + w, false);
    }
    track.scrollBy({left: dir * cardW(), behavior:'smooth'});
    setTimeout(normalize, 500);
  }
  if(prev) prev.addEventListener('click', function(){ go(-1); });
  if(next) next.addEventListener('click', function(){ go(1); });
  var normTimer=null;
  track.addEventListener('scroll', function(){
    if(normTimer) clearTimeout(normTimer);
    normTimer=setTimeout(normalize, 120);
  }, {passive:true});
  var timer=null;
  function play(){
    stop();
    if(!window.matchMedia('(min-width: 992px)').matches) return;
    if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    timer=setInterval(function(){ go(1); }, 4000);
  }
  function stop(){ if(timer){ clearInterval(timer); timer=null; } }
  row.addEventListener('mouseenter', stop);
  row.addEventListener('mouseleave', play);
  row.addEventListener('focusin', stop);
  row.addEventListener('focusout', play);
  row.addEventListener('touchstart', stop, {passive:true});
  play();
})();
</script>
@endif
