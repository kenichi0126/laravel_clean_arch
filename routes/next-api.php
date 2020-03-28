<?php

Route::namespace('CommercialAdvertising\Get')
    ->prefix('commercials')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('advertising', 'Controller@index')->middleware(['check_exists_custom_indicator']);
        Route::get('advertisingCsv', 'Controller@index');
    });

Route::namespace('CommercialList\Get')
    ->prefix('commercials')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('list', 'Controller@index')->middleware(['check_exists_custom_indicator']);
        Route::get('listCsv', 'Controller@index');
    });

Route::namespace('CommercialGrp\Get')
    ->prefix('commercials')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('grp', 'Controller@index')->middleware(['check_exists_custom_indicator']);
        Route::get('grpCsv', 'Controller@index');
    });

Route::namespace('RankingCommercial\Get')
    ->prefix('rankings')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('commercial', 'Controller@index');
        Route::get('commercialCsv', 'Controller@index');
    });

Route::namespace('RatingPerHourly\Get')
    ->prefix('rating')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('perHourly', 'Controller@index')->middleware(['check_exists_custom_indicator']);
        Route::get('perHourlyCsv', 'Controller@index');
    });

Route::namespace('RatingPerMinutes\Get')
    ->prefix('rating')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('perMinutes', 'Controller@index');
        Route::get('perMinutesCsv', 'Controller@index');
    });

Route::namespace('SampleCount\Get')
    ->prefix('sample_count')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('get_sample_count', 'Controller@index');
    });

Route::namespace('Questions\Get')
    ->prefix('sample_count')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('get_questions', 'Controller@index');
    });

Route::namespace('Categories\Get')
    ->prefix('categories')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::get('/', 'Controller@index');
    });

Route::namespace('ProductNames\Get')
    ->prefix('product_names')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/', 'Controller@index');
    });

Route::namespace('CompanyNames\Get')
    ->prefix('company_names')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/', 'Controller@index');
    });

Route::namespace('ProgramNames\Get')
    ->prefix('program_names')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/', 'Controller@index');
    });

Route::namespace('Top\Get')
    ->prefix('top')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::get('/', 'Controller@index');
    });

Route::namespace('TopRanking\Get')
    ->prefix('top')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::get('/ranking', 'Controller@index');
    });

Route::namespace('TimeBox\Get')
    ->prefix('timebox')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::get('/latest', 'Controller@index');
    });

Route::namespace('PanelStructure\Get')
    ->prefix('panel_structure')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/', 'Controller@index');
    });

Route::namespace('RafChart\Get')
    ->prefix('raf')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/highchart', 'Controller@index');
    });

Route::namespace('RafCsv\Get')
    ->prefix('raf')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/csv', 'Controller@index');
    });

Route::namespace('Divisions\Get')
    ->prefix('division')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::get('/', 'Controller@index');
    });

Route::namespace('ProgramList\Get')
    ->prefix('programs')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/', 'Controller@index')->middleware(['check_exists_custom_indicator']);
        Route::get('listCsv', 'Controller@index');
    });

Route::namespace('ProgramTable\Get')
    ->prefix('programs')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/table', 'Controller@index')->middleware(['check_exists_custom_indicator']);
    });

Route::namespace('ProgramTableDetail\Get')
    ->prefix('programs')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/tableDetail', 'Controller@index');
    });

Route::namespace('ProgramPeriodAverage\Get')
    ->prefix('programs')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/periodAverage', 'Controller@index')->middleware(['check_exists_custom_indicator']);
        Route::get('/periodAverageCsv', 'Controller@index');
    });

Route::namespace('SearchConditions\Get')
    ->prefix('search_conditions')
    ->middleware(['auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output'])->group(function (): void {
        Route::get('/', 'Controller@index');
    });

Route::namespace('SearchConditions\Create')
    ->prefix('search_conditions')
    ->middleware(['auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output'])->group(function (): void {
        Route::post('/create', 'Controller@index');
    });

Route::namespace('SearchConditions\Update')
    ->prefix('search_conditions')
    ->middleware(['auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output'])->group(function (): void {
        Route::post('/update', 'Controller@index');
    });

Route::namespace('SearchConditions\Delete')
    ->prefix('search_conditions')
    ->middleware(['auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output'])->group(function (): void {
        Route::get('/delete', 'Controller@index');
    });

Route::namespace('ProgramListAverage\Get')
    ->prefix('programs')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/average', 'Controller@index');
    });

Route::namespace('ProgramLatestDate\Get')
    ->prefix('programs')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::get('/latestDate', 'Controller@index');
    });

Route::namespace('ProgramMultiChannelProfile\Get')
    ->prefix('programs')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/multiChannelProfileCsv', 'Controller@index');
    });

Route::namespace('SystemNotice\Create')
    ->prefix('notice')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/readsn', 'Controller@index');
    });

Route::namespace('UserNotice\Create')
    ->prefix('notice')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/readun', 'Controller@index');
    });

Route::namespace('CmMaterials\Get')
    ->prefix('cm_materials')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/', 'Controller@index');
    });

Route::namespace('HourlyReport\Get')
    ->prefix('hourlyreport')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::get('/latest', 'Controller@index');
    });

Route::namespace('Channels\Get')
    ->prefix('channels')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/', 'Controller@index');
    });

Route::namespace('MdataProgGenres\Get')
    ->prefix('mdata_prog_genre')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::get('/', 'Controller@index');
    });

Route::namespace('MdataCmGenres\Get')
    ->prefix('mdata_cm_genre')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::get('/', 'Controller@index');
    });

Route::namespace('SettingAttrDivs\Create')
    ->prefix('setting')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/createAttrDivs', 'Controller@index');
    });

Route::namespace('SettingAttrDivs\Delete')
    ->prefix('setting')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::get('/removeAttrDivs', 'Controller@index');
    });

Route::namespace('SettingAttrDivs\Update')
    ->prefix('setting')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/updateAttrDivs', 'Controller@index');
    });

Route::namespace('SettingAttrDivs\Get')
    ->prefix('setting')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::get('/originalDivisions', 'Controller@index');
    });

Route::namespace('Setting\Save')
    ->prefix('setting')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/updateSetting', 'Controller@index');
    });

Route::namespace('SettingAggregate\Get')
    ->prefix('setting')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::get('/aggregateSetting', 'Controller@index');
    });

Route::namespace('SettingAttrDivsOrder\Update')
    ->prefix('setting')
    ->middleware('auth:api', 'auth', 'sametime_login', 'check_smart_api_version', 'apitime', 'presenter_output')->group(function (): void {
        Route::post('/updateOrder', 'Controller@index');
    });
