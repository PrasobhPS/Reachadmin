<div class="slider-head-clinicdate">
    <div class="f1">
        <h6 class="slider-sub-head-clinicdate">Morning Session</h6>
    </div>
</div>
<div class="time-btn-sec">
    @foreach ($morningSlots as $time)
        @php $formattedTime = date('H:i:s', strtotime($time)); @endphp
        <label class="appointment_time"><input type="checkbox" name="schedule_time[]" value="{{ $formattedTime }}" @if (in_array($formattedTime, $startTimes)) checked @endif> {{ $time }}</label>
    @endforeach
</div>
<div class="slider-head-clinicdate">
    <div class="f1">
        <h6 class="slider-sub-head-clinicdate">Afternoon Session</h6>
    </div>
</div>
<div class="time-btn-sec">
    @foreach ($afternoonSlots as $time)
        @php $formattedTime = date('H:i:s', strtotime($time)); @endphp
        <label class="appointment_time"><input type="checkbox" name="schedule_time[]" value="{{ $formattedTime }}" @if (in_array($formattedTime, $startTimes)) checked @endif> {{ $time }}</label>
    @endforeach
</div>
<div class="slider-head-clinicdate">
    <div class="f1">
        <h6 class="slider-sub-head-clinicdate">Evening Session</h6>
    </div>
</div>
<div class="time-btn-sec">
    @foreach ($eveningSlots as $time)
        @php $formattedTime = date('H:i:s', strtotime($time)); @endphp
        <label class="appointment_time"><input type="checkbox" name="schedule_time[]" value="{{ $formattedTime }}" @if (in_array($formattedTime, $startTimes)) checked @endif> {{ $time }}</label>
    @endforeach
</div>

<br>
<label id="schedule_time[]-error" class="error" for="schedule_time[]"></label>