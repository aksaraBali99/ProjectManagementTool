{{--
    task #121: the page's return_to / return_label as hidden inputs, so a
    save or a Deactivate/Activate lands back on the edit page with the same
    back link. Only what the page actually received
    ($returnToParams, from ResolvesReturnTo::returnToParams()): the Tasks
    fallback is never sent as an explicit value. old() keeps them through a
    validation error. The controller validates them again on submit.
--}}
@php
    $returnToValue = old('return_to', $returnToParams['return_to'] ?? null);
    $returnLabelValue = old('return_label', $returnToParams['return_label'] ?? null);
@endphp
@if (is_string($returnToValue))
    <input type="hidden" name="return_to" value="{{ $returnToValue }}">
@endif
@if (is_string($returnLabelValue))
    <input type="hidden" name="return_label" value="{{ $returnLabelValue }}">
@endif
