<!--tab start-->
<div class="col-md-12 col-sm-9 col-xs-12 hz-tab-container">
    <!--MENUS-->
    <div class="col-md-2 col-sm-3 col-xs-3 hz-tab-menu">
        <div class="list-group">
            @foreach($options as $option)
                <a href="#" class="list-group-item @if($option->id=='general') active @endif text-center">
                    <h4 class="fa {{$option->icon}} fa-2x"></h4><br/>{{$option->label}}
                </a>
            @endforeach
        </div>
    </div>
    <!--#MENUS-->

    <!--TAB CONTENT-->
    <div class="col-md-10 col-sm-9 col-xs-9 hz-tab">
        @foreach($options as $option)

            <div class="hz-tab-content h_tab_content @if($option->id=='general') active @endif">
                <form action="{{url(config('option-framework.view_route_path'))}}" method="post">

                    @foreach($option->fields as $field)
                        <p>
                            <strong><i class="fa {{getIcon($field)}}"></i> {{$field->label}}</strong>
                            @if(isset($field->description))(<span>{{$field->description}}</span>)@endif
                        </p>

                        <div class="@if($errors->has($field->id)) has-error @endif option-item">
                            <?php
                                $value = getOption($field->id);
                            ?>

                            @if($field->type=='switcher')
                                <input type="hidden" name="{{$field->id}}" value="0">
                                <input type="checkbox" name="{{$field->id}}" @if($value) checked @endif value="1"
                                       class="form-control bs-switch">

                            @elseif($field->type=='dropdown')
                                <?php
                                $dtOptions = $field->options;
                                if (is_string($dtOptions)) {
                                    $opConfig = explode(',', $dtOptions);
                                    $dtTable = isset($opConfig[0]) ? $opConfig[0] : '';
                                    $dtKeyCol = isset($opConfig[0]) ? $opConfig[1] : '';
                                    $dtValCol = isset($opConfig[0]) ? $opConfig[2] : '';

                                    $optionsFromDb = DB::table($dtTable)
                                        ->select([DB::raw($dtKeyCol . ' as valCol'), DB::raw($dtValCol . ' as lblCol')])
                                        ->get()->toArray();
                                }

                                ?>
                                <select class="form-control select2" name="{{$field->id}}">
                                    <option value="">--select--</option>
                                    @if(is_string($dtOptions))
                                        @foreach($optionsFromDb as $row)
                                            <option @if($row->valCol==$value) selected
                                                    @endif value="{{$row->valCol}}">{{$row->lblCol}}</option>
                                        @endforeach
                                    @elseif(isAssoc($dtOptions))
                                        @foreach($dtOptions as $opValue => $label)
                                            <option @if($opValue==$value) selected
                                                    @endif value="{{$opValue}}">{{$label}}</option>
                                        @endforeach

                                    @else
                                        @foreach($dtOptions as $item)
                                            <option @if($item==$value) selected
                                                    @endif value="{{$item}}">{{$item}}</option>
                                        @endforeach
                                    @endif
                                </select>

                            @elseif($field->type=='icon')
                                    <select class="form-control icon_select_box" data-selected="{{$value}}"
                                            name="{{$field->id}}">
                                        <option value="">--select--</option>
                                        @include('OptionFramework::partials.icon')
                                    </select>
                            @elseif($field->type=='radio')
                                <div class="radio_box">
                                    @foreach($field->options as $key => $item)
                                        <input type="radio" id="{{$field->id.$key}}"
                                               @if($item==$value) checked @endif
                                               name="{{$field->id}}" value="{{$item}}">
                                        <label for="{{$field->id.$key}}">{{$item}}</label><br>
                                    @endforeach
                                </div>
                            @elseif($field->type=='multicheck')
                                <div class="multicheck_box">
                                    <input type="hidden" class="multicheck_input" name="{{$field->id}}"
                                           value="{{$value}}">
                                    @foreach($field->options as $key => $item)
                                        <input type="checkbox" id="{{$field->id.$key}}" value="{{$item}}">
                                        <label for="{{$field->id.$key}}">{{$item}}</label><br>
                                    @endforeach
                                </div>
                            @elseif($field->type=='autocomplete')
                                <input data-options="{{$field->options}}"
                                       class="form-control autocomplete"
                                       name="{{$field->id}}"
                                       value="{{$value}}">

                            @elseif($field->type=='tag')
                                <input type="text"
                                       name="{{$field->id}}" value="{{$value}}"
                                       class="form-control tag">

                            @elseif($field->type=='datepicker')
                                <input type="text" name="{{$field->id}}" value="{{$value}}"
                                       class="form-control datepicker">

                            @elseif($field->type=='timepicker')
                                <input type="text" name="{{$field->id}}" value="{{$value}}"
                                       class="form-control timepicker">

                            @elseif($field->type=='editor')
                                <textarea name="{{$field->id}}" id="{{$field->id}}" cols="30"
                                          class="form-control editor" rows="10">{{$value}}</textarea>

                            @elseif($field->type=='textarea')
                                <textarea name="{{$field->id}}" id="{{$field->id}}" class="form-control"
                                          rows="6">{{$value}}</textarea>

                            @elseif($field->type=='colorpicker')
                                <div class="input-group clr_picker">
                                    <input type="text" readonly="readonly" name="{{$field->id}}" value="{{$value}}"
                                           class="form-control">
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            @else
                                <input type="{{$field->type}}" name="{{$field->id}}" value="{{$value}}"
                                       placeholder="{{isset($field->placeholder)?$field->placeholder:''}}"
                                       class="form-control">
                            @endif

                            @if($errors->has($field->id))
                                <p class="error_msg">{{ $errors->first($field->id) }}</p>
                            @endif
                            <div class="bottom_space"></div>
                        </div>
                        <!--end input-item -->
                    @endforeach

                    <div class="well well-sm save_well">
                        <button type="submit" class="btn btn-primary btnSaveOption">Save</button>
                    </div>
                </form>
            </div>
            <!-- end section -->
        @endforeach
    </div>
    <!--#TAB CONTENT-->
</div>
<!--tab wrapper end -->