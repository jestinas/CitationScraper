@foreach($output as $d)
<div class="data-row" data-ll-id="google" data-listing-id="wrong_phone_number" data-filter-item="" data-fetch-status="1">
    <div class="site-detail">
        <div class="site-info">
            <div class="left">
                <img src="https://s3.amazonaws.com/www.yextstudio.com/BusinessScan/medallions/2016/08/medallion-google.svg">
            </div>
            <div class="right">
                <span>google</span>
                <a href="https://www.google.com/maps?cid=1612730502560836674" target="_blank">View Listing</a>
            </div>
        </div>
    </div>
    <div class="listing-info text-center">
        <div class="info">
            <div class="b-name">
                Test Business
            </div>
            <div class="address">
                Test Address
            </div>
            <div class="phone error">
                9872135872
            </div>
            <div class="status">
                <span class="tag-status wrong-phone-number">Wrong Phone Number</span>
            </div>
        </div>
    </div>
</div>
@endforeach