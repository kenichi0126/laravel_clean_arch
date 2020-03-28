--
-- PostgreSQL database dump
--

-- Dumped from database version 9.6.10
-- Dumped by pg_dump version 10.5

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: apgcc; Type: SCHEMA; Schema: -; Owner: switch
--

CREATE SCHEMA apgcc;


ALTER SCHEMA apgcc OWNER TO switch;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner:
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner:
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: bs_query_household_viewing_data(integer, timestamp without time zone, timestamp without time zone, integer, interval, text); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.bs_query_household_viewing_data(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pchannelid integer DEFAULT NULL::integer, psplitinterval interval DEFAULT NULL::interval, pchannelconversionmode text DEFAULT NULL::text) RETURNS TABLE(time_box_id integer, term_from timestamp without time zone, term_to timestamp without time zone, household_id integer, started_at timestamp without time zone, ended_at timestamp without time zone, channel_id integer, viewing_seconds integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
--パラメータチェック
IF pRegionId IS NULL THEN
RAISE EXCEPTION 'パラメータpRegionIdがnullです。';
END IF;
IF pTermFrom IS NULL THEN
RAISE EXCEPTION 'パラメータpTermFromがnullです。';
END IF;
IF pTermTo IS NULL THEN
RAISE EXCEPTION 'パラメータpTermToがnullです。';
END IF;

IF pSplitInterval IS NULL THEN
pSplitInterval := pTermTo - pTermFrom;
END IF;

--実行＆返却
RETURN QUERY
select
a.time_box_id,
a.term_from,
a.term_to,
a.household_id,
min(a.started_at) as started_at,
max(a.ended_at) as ended_at,
a.channel_id,
extract(epoch from (max(a.ended_at) - min(a.started_at)))::integer as viewing_seconds
from (
select
a.*,
sum(a.top_flag) over(partition by a.household_id, a.channel_id order by a.started_at asc, a.ended_at asc) as seq
from (
select
a.*,
case when a.started_at <= max(a.ended_at) over(partition by a.time_box_id, a.household_id, a.channel_id order by a.started_at asc, a.ended_at asc rows between unbounded preceding and 1 preceding) then 0 else 1 end as top_flag
from (
select
tb.id as time_box_id,
h.term_from,
h.term_to,
tp.household_id,
greatest(ad.started_at, tb.started_at, h.term_from, pTermFrom) as started_at,
least(ad.ended_at, tb.ended_at, h.term_to, pTermTo) as ended_at,
cc.channel_id
from audience_data ad
join (
select
h.h as term_from,
h.h + pSplitInterval as term_to
from generate_series (
pTermFrom,
pTermTo - interval '1 second',
pSplitInterval
) h
) h on ad.started_at < h.term_to and ad.ended_at > h.term_from
join time_boxes tb on tb.region_id = pRegionId and tb.started_at < pTermTo and tb.ended_at > pTermFrom
and tb.started_at < ad.ended_at and tb.ended_at > ad.started_at
join time_box_panelers tp on tp.time_box_id = tb.id and tp.paneler_id = ad.paneler_id
join query_converted_channels(pChannelId, pChannelConversionMode) cc on cc.base_channel_id = ad.channel_id
where ad.region_id = pRegionId and ad.started_at < pTermTo and ad.ended_at > pTermFrom
) a
) a
) a
group by a.time_box_id, a.term_from, a.term_to, a.household_id, a.channel_id, a.seq
;
RETURN;
END;
$$;


ALTER FUNCTION public.bs_query_household_viewing_data(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pchannelid integer, psplitinterval interval, pchannelconversionmode text) OWNER TO switch;

--
-- Name: bs_query_personal_viewing_data(integer, timestamp without time zone, timestamp without time zone, integer, interval, text); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.bs_query_personal_viewing_data(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pchannelid integer DEFAULT NULL::integer, psplitinterval interval DEFAULT NULL::interval, pchannelconversionmode text DEFAULT NULL::text) RETURNS TABLE(time_box_id integer, term_from timestamp without time zone, term_to timestamp without time zone, paneler_id integer, base_unit_id integer, household_id integer, started_at timestamp without time zone, ended_at timestamp without time zone, channel_id integer, viewing_seconds integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
--パラメータチェック
IF pRegionId IS NULL THEN
RAISE EXCEPTION 'パラメータpRegionIdがnullです。';
END IF;
IF pTermFrom IS NULL THEN
RAISE EXCEPTION 'パラメータpTermFromがnullです。';
END IF;
IF pTermTo IS NULL THEN
RAISE EXCEPTION 'パラメータpTermToがnullです。';
END IF;

IF pSplitInterval IS NULL THEN
pSplitInterval := pTermTo - pTermFrom;
END IF;

--実行＆返却
RETURN QUERY
select
a.time_box_id,
a.term_from,
a.term_to,
a.paneler_id,
a.base_unit_id,
a.household_id,
a.started_at,
a.ended_at,
a.channel_id,
extract(epoch from (a.ended_at - a.started_at))::integer as viewing_seconds
from (
select
tb.id as time_box_id,
h.term_from,
h.term_to,
ad.paneler_id,
ad.base_unit_id,
tp.household_id,
greatest(ad.started_at, tb.started_at, h.term_from, pTermFrom) as started_at,
least(ad.ended_at, tb.ended_at, h.term_to, pTermTo) as ended_at,
cc.channel_id
from audience_data ad
join (
select
h.h as term_from,
h.h + pSplitInterval as term_to
from generate_series (
pTermFrom,
pTermTo - interval '1 second',
pSplitInterval
) h
) h on ad.started_at < h.term_to and ad.ended_at > h.term_from
join time_boxes tb on tb.region_id = pRegionId and tb.started_at < pTermTo and tb.ended_at > pTermFrom
and tb.started_at < ad.ended_at and tb.ended_at > ad.started_at
join time_box_panelers tp on tp.time_box_id = tb.id and tp.paneler_id = ad.paneler_id
join query_converted_channels(pChannelId, pChannelConversionMode) cc on cc.base_channel_id = ad.channel_id
where ad.region_id = pRegionId and ad.started_at < pTermTo and ad.ended_at > pTermFrom
) a
;
RETURN;
END;
$$;


ALTER FUNCTION public.bs_query_personal_viewing_data(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pchannelid integer, psplitinterval interval, pchannelconversionmode text) OWNER TO switch;

--
-- Name: bs_query_personal_viewing_data_with_attr(integer, timestamp without time zone, timestamp without time zone, character varying, integer, interval, text); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.bs_query_personal_viewing_data_with_attr(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pdivision character varying, pchannelid integer DEFAULT NULL::integer, psplitinterval interval DEFAULT NULL::interval, pchannelconversionmode text DEFAULT NULL::text) RETURNS TABLE(time_box_id integer, term_from timestamp without time zone, term_to timestamp without time zone, paneler_id integer, division character varying, code character varying, base_unit_id integer, household_id integer, started_at timestamp without time zone, ended_at timestamp without time zone, channel_id integer, viewing_seconds integer)
    LANGUAGE plpgsql
    AS $_$
DECLARE
vQuery text;
BEGIN
--パラメータチェック
IF pRegionId IS NULL THEN
RAISE EXCEPTION 'パラメータpRegionIdがnullです。';
END IF;
IF pTermFrom IS NULL THEN
RAISE EXCEPTION 'パラメータpTermFromがnullです。';
END IF;
IF pTermTo IS NULL THEN
RAISE EXCEPTION 'パラメータpTermToがnullです。';
END IF;
IF pDivision IS NULL THEN
RAISE EXCEPTION 'パラメータpDivisionがnullです。';
END IF;

--クエリの組み立て
vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || '   v.time_box_id,';
vQuery := vQuery || '   v.term_from,';
vQuery := vQuery || '   v.term_to,';
vQuery := vQuery || '   v.paneler_id,';
vQuery := vQuery || '   $4 as division,';
vQuery := vQuery || '   tp.code,';
vQuery := vQuery || '   v.base_unit_id,';
vQuery := vQuery || '   v.household_id,';
vQuery := vQuery || '   v.started_at,';
vQuery := vQuery || '   v.ended_at,';
vQuery := vQuery || '   v.channel_id,';
vQuery := vQuery || '   v.viewing_seconds';
vQuery := vQuery || ' from bs_query_personal_viewing_data($1, $2, $3, $5, $6, $7) v';
vQuery := vQuery || ' left join query_panelers_with_attr($1, $2, $3, $4) tp';
vQuery := vQuery || '   on tp.time_box_id = v.time_box_id and tp.paneler_id = v.paneler_id';
--RAISE NOTICE '%', vQuery;

--実行＆返却
RETURN QUERY
EXECUTE vQuery
USING pRegionId, pTermFrom, pTermTo, pDivision, pChannelId, pSplitInterval, pChannelConversionMode
;
RETURN;
END;
$_$;


ALTER FUNCTION public.bs_query_personal_viewing_data_with_attr(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pdivision character varying, pchannelid integer, psplitinterval interval, pchannelconversionmode text) OWNER TO switch;

--
-- Name: bs_query_viewing_rate(integer, timestamp without time zone, timestamp without time zone, integer, character varying, interval, text); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.bs_query_viewing_rate(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pchannelid integer, pdivision character varying, psplitinterval interval DEFAULT NULL::interval, pchannelconversionmode text DEFAULT NULL::text) RETURNS TABLE(region_id integer, term_from timestamp without time zone, term_to timestamp without time zone, channel_id integer, division character varying, code character varying, viewing_seconds bigint, viewing_rate real)
    LANGUAGE plpgsql
    AS $_$
DECLARE
rTimeBoxSummary RECORD;
vQuery text;
BEGIN
--パラメータチェック
IF pRegionId IS NULL THEN
RAISE EXCEPTION 'パラメータpRegionIdがnullです。';
END IF;
IF pTermFrom IS NULL THEN
RAISE EXCEPTION 'パラメータpTermFromがnullです。';
END IF;
IF pTermTo IS NULL THEN
pTermTo := pTermFrom + interval '1 second';
END IF;
IF pDivision IS NULL THEN
RAISE EXCEPTION 'パラメータpDivisionがnullです。';
END IF;

--対象タイムボックスのチェック
select
count(*) as cnt,
min(tb.started_at) as min_started_at,
max(tb.ended_at) as max_ended_at
into rTimeBoxSummary
from time_boxes tb
where tb.region_id = pRegionId
  and tb.started_at < pTermTo and tb.ended_at > pTermFrom;
--存在チェック
IF rTimeBoxSummary.cnt = 0 THEN
RAISE EXCEPTION '対象のタイムボックスが見つかりません。 pRegionId=%, pTerm=% ～ %', pRegionId, pTermFrom, pTermTo;
END IF;
--pTermFromの範囲チェック
IF pTermFrom < rTimeBoxSummary.min_started_at THEN
RAISE EXCEPTION 'pTermFromが範囲外です。 pTermFrom=%, LIMIT=%', pTermFrom, rTimeBoxSummary.min_started_at;
END IF;
--pTermToの範囲チェック
IF pTermTo > rTimeBoxSummary.max_ended_at THEN
RAISE EXCEPTION 'pTermToが範囲外です。 pTermTo=%, LIMIT=%', pTermTo, rTimeBoxSummary.max_ended_at;
END IF;

--クエリの組み立て
vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || '   $1 as region_id,';
vQuery := vQuery || '   q1.term_from,';
vQuery := vQuery || '   q1.term_to,';
vQuery := vQuery || '   q1.channel_id,';
vQuery := vQuery || '   $5 as division,';
vQuery := vQuery || '   q1.code::varchar,';
vQuery := vQuery || '   sum(q1.viewing_seconds)::bigint as viewing_seconds,';
vQuery := vQuery || '   (sum(q1.viewing_seconds)::numeric / NULLIF(sum(tn.number * least(tbq.seconds, extract(epoch from $6))), 0) * 100)::real as viewing_rate';
vQuery := vQuery || ' from (';
vQuery := vQuery || '   select';
vQuery := vQuery || '     v.term_from,';
vQuery := vQuery || '     v.term_to,';
vQuery := vQuery || '     v.time_box_id,';
vQuery := vQuery || '     v.channel_id,';
vQuery := vQuery || '     v.code,';
vQuery := vQuery || '     sum(v.viewing_seconds)::bigint as viewing_seconds';
vQuery := vQuery || '   from (';
CASE pDivision
WHEN 'personal' THEN
vQuery := vQuery || ' select';
vQuery := vQuery || '   v.*,';
vQuery := vQuery || '   ''1''::varchar as code';
vQuery := vQuery || ' from bs_query_personal_viewing_data($1, $2, $3, $4, $6, $7) v';
WHEN 'household' THEN
vQuery := vQuery || ' select';
vQuery := vQuery || '   v.*,';
vQuery := vQuery || '   ''1''::varchar as code';
vQuery := vQuery || ' from bs_query_household_viewing_data($1, $2, $3, $4, $6, $7) v';
ELSE
vQuery := vQuery || ' select';
vQuery := vQuery || '   v.*';
vQuery := vQuery || ' from bs_query_personal_viewing_data_with_attr($1, $2, $3, $5, $4, $6, $7) v';
END CASE;
vQuery := vQuery || '   ) v';
vQuery := vQuery || '   where v.code is not null';
vQuery := vQuery || '   group by v.term_from, v.term_to, v.time_box_id, v.channel_id, v.code';
vQuery := vQuery || ' ) q1';
--オリジナル区分の場合は有効パネル数を計算する
vQuery := vQuery || ' left join';
IF pDivision NOT IN ('personal', 'household') THEN
vQuery := vQuery || ' query_samples(q1.time_box_id, $5)';
ELSE
vQuery := vQuery || ' time_box_attr_numbers';
END IF;
vQuery := vQuery || ' tn on tn.time_box_id = q1.time_box_id and tn.division = $5 and tn.code = q1.code';
vQuery := vQuery || ' left join (';
vQuery := vQuery || '   select';
vQuery := vQuery || '     id,';
vQuery := vQuery || '     extract(epoch from (least(ended_at, $3) - greatest(started_at, $2))) as seconds';
vQuery := vQuery || '   from time_boxes';
vQuery := vQuery || '   where region_id = $1 and started_at < $3 and ended_at > $2';
vQuery := vQuery || ' ) tbq on tbq.id = q1.time_box_id';
vQuery := vQuery || ' group by q1.term_from, q1.term_to, q1.channel_id, q1.code';
--RAISE NOTICE '%', vQuery;

--実行＆返却
RETURN QUERY
EXECUTE vQuery
USING pRegionId, pTermFrom, pTermTo, pChannelId, pDivision, pSplitInterval, pChannelConversionMode
;
RETURN;
END;
$_$;


ALTER FUNCTION public.bs_query_viewing_rate(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pchannelid integer, pdivision character varying, psplitinterval interval, pchannelconversionmode text) OWNER TO switch;

--
-- Name: build_attr_condition(text, character varying); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.build_attr_condition(pdefinition text, palias character varying DEFAULT NULL::character varying) RETURNS text
    LANGUAGE plpgsql
    AS $$
DECLARE
vResult text;
vDefs text[];
iDef integer;
vColumn text;
vColumnType text;
vQ text;
vVal text;
vValues text[];
BEGIN
--パラメータチェック
IF pAlias IS NOT NULL AND pAlias <> '' THEN
pAlias := pAlias || '.';
ELSE
pAlias := '';
END IF;

IF pDefinition IS NULL THEN
vResult := '1 = 0';
ELSEIF pDefinition IN ('personal', 'household') THEN
vResult := '1 = 1';
ELSE
vResult := '';
vDefs := regexp_split_to_array(pDefinition, ':');
FOR iDef IN 1 .. array_length(vDefs, 1) LOOP

vColumn := split_part(vDefs[iDef], '=', 1);

--カラムの型によりクォーテーションを付ける
select data_type into vColumnType
from information_schema.columns
where table_name = 'time_box_panelers' and column_name = vColumn;
vQ := '''';
IF vColumnType LIKE '%int%' THEN
vQ := '';
END IF;

vColumn := pAlias || vColumn;
vVal := split_part(vDefs[iDef], '=', 2);

IF iDef > 1 THEN
vResult := vResult || ' and ';
END IF;

IF strpos(vVal, ',') > 0 AND strpos(vVal, '-') > 0 THEN
RAISE EXCEPTION '定義情報に、","と"-"の両方を指定することはできません。 division=%, code=%, definition=%', pDivision, rAttrDiv.code, rAttrDiv.definition;
ELSIF strpos(vVal, ',') > 0 THEN
vValues := regexp_split_to_array(vVal, ',');
vResult := vResult || vColumn || ' in (' || vQ || array_to_string(vValues, vQ || ', ' || vQ) || vQ || ')';
ELSIF strpos(vVal, '-') > 0 THEN
vValues := regexp_split_to_array(vVal, '-');
vResult := vResult || vColumn || ' between ' || vQ || vValues[1] || vQ || ' and ' || vQ || vValues[2] || vQ || '';
ELSE
vResult := vResult || vColumn || ' = ' || vQ || vVal || vQ || '';
END IF;
END LOOP;
END IF;

RETURN vResult;
END;
$$;


ALTER FUNCTION public.build_attr_condition(pdefinition text, palias character varying) OWNER TO switch;

--
-- Name: calc_dashb_term(character varying, date, date, timestamp without time zone, character varying); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.calc_dashb_term(ptermtype character varying, pcustomfrom date DEFAULT NULL::date, pcustomto date DEFAULT NULL::date, pdatetime timestamp without time zone DEFAULT ('now'::text)::timestamp without time zone, pmode character varying DEFAULT NULL::character varying) RETURNS TABLE(term_type character varying, term_from date, term_to date)
    LANGUAGE plpgsql
    AS $$
DECLARE
vNow timestamp;
vToday date;
vBaseDate date;
vFrom date;
vTo date;
BEGIN

vNow := localtimestamp;
vToday := date_trunc('day', vNow)::date;

--対象日時の補正
IF pDatetime IS NULL THEN
pDatetime := vNow;
END IF;

--基準日
vBaseDate := date_trunc('day', pDatetime)::date;
--CMモードの場合は基準日を補正
IF pMode ILIKE 'cm' THEN
vBaseDate := least(vBaseDate, vToday - interval '2 days');
END IF;

IF pTermType = 'custom' THEN
--任意期間（カレンダー指定）
vFrom := pCustomFrom;
vTo := least(pCustomTo, vBaseDate - interval '1 day');
ELSEIF pTermType = 'last_7_days' THEN
--直近7日間
vFrom := vBaseDate - interval '7 day';
vTo := vBaseDate - interval '1 day';
ELSEIF pTermType = 'last_14_days' THEN
--直近14日間
vFrom := vBaseDate - interval '14 day';
vTo := vBaseDate - interval '1 day';
ELSEIF pTermType = 'last_28_days' THEN
--直近28日間
vFrom := vBaseDate - interval '28 day';
vTo := vBaseDate - interval '1 day';
ELSEIF pTermType = 'last_week' THEN
--先週
vTo := vBaseDate;
LOOP
vTo := vTo - interval '1 day';
IF extract(dow from vTo) = 0 THEN
EXIT;
END IF;
END LOOP;
vFrom := vTo - interval '6 day';
ELSEIF pTermType = 'last_month' THEN
--先月
vFrom := date_trunc('month', vBaseDate) - interval '1 month';
vTo := date_trunc('month', vBaseDate) - interval '1 day';
ELSEIF pTermType = 'last_quarter' THEN
--前四半期
vTo := date_trunc('month', vBaseDate);
LOOP
IF extract(month from vTo) in (1, 4, 7, 10) THEN
EXIT;
END IF;
vTo := vTo - interval '1 month';
END LOOP;
vFrom := vTo - interval '3 month';
vTo := vTo - interval '1 day';
END IF;

--返却
RETURN QUERY select pTermType, vFrom, vTo;
END;
$$;


ALTER FUNCTION public.calc_dashb_term(ptermtype character varying, pcustomfrom date, pcustomto date, pdatetime timestamp without time zone, pmode character varying) OWNER TO switch;

--
-- Name: seq_tuner_event_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_tuner_event_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_tuner_event_id OWNER TO switch;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: tuner_events; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.tuner_events (
    id bigint DEFAULT nextval('public.seq_tuner_event_id'::regclass) NOT NULL,
    region_id integer NOT NULL,
    paneler_id integer NOT NULL,
    channel_id integer NOT NULL,
    base_unit_id integer NOT NULL,
    occurred_at timestamp(0) without time zone NOT NULL,
    usec integer NOT NULL,
    processed integer DEFAULT 0 NOT NULL,
    recorded_at timestamp(0) without time zone,
    record_type integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.tuner_events OWNER TO switch;

--
-- Name: process_tuner_event(public.tuner_events); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.process_tuner_event(rtunerevent public.tuner_events) RETURNS SETOF text
    LANGUAGE plpgsql
    AS $$
DECLARE
	rCurrData audience_data%ROWTYPE;
	rTmpData audience_data%ROWTYPE;
	rTmpEvent tuner_events%ROWTYPE;
	vMsgPrefix text;
BEGIN

	IF rTunerEvent IS NULL THEN
		RAISE EXCEPTION 'パラメータrTunerEventがnullです。';
	END IF;

	vMsgPrefix := format('tuner_event{%s,%s,%s}', rTunerEvent.id, rTunerEvent.paneler_id, rTunerEvent.occurred_at);

	-- 処理済みフラグを更新
	update tuner_events set processed = 1 where id = rTunerEvent.id;

	-- 処理済みチェック
	IF rTunerEvent.processed = 1 THEN
		RETURN NEXT format('%s skip(processed)', vMsgPrefix);
		RETURN;
	END IF;

	-- 登録済みチェック
	select
		* into rTmpData
	from audience_data
	where tuner_event_id = rTunerEvent.id
	;
	IF FOUND THEN
		RETURN NEXT format('%s skip(processed)', vMsgPrefix);
		RETURN;
	END IF;

	-- キー重複チェック
	select
		* into rTmpData
	from audience_data
	where paneler_id = rTunerEvent.paneler_id
	  and started_at = rTunerEvent.occurred_at
	;
	IF FOUND THEN
		IF rTmpData.started_usec > rTunerEvent.usec
			OR (rTmpData.started_usec = rTunerEvent.usec AND rTmpData.tuner_event_id >= rTunerEvent.id) THEN
			-- 登録対象外
			RETURN NEXT format('%s skip(priority)', vMsgPrefix);
			RETURN;
		ELSE
			-- 既存のaudience_dataを削除
			delete from audience_data where tuner_event_id = rTmpData.tuner_event_id;
			RETURN NEXT format('%s delete audience_data{%s,%s,%s,%s} < %s',
				vMsgPrefix, rTmpData.tuner_event_id, rTmpData.paneler_id, rTmpData.started_at, rTmpData.started_usec, rTunerEvent.usec);
		END IF;
	END IF;

	-- 同秒tuner_event内での優先度判定
	select
		* into rTmpEvent
	from tuner_events
	where paneler_id = rTunerEvent.paneler_id
	  and occurred_at = rTunerEvent.occurred_at
	  and usec > rTunerEvent.usec
	  and processed = 1
	limit 1
	;
	IF FOUND THEN
		-- 登録対象外
		RETURN NEXT format('%s skip(priority)', vMsgPrefix);
		RETURN;
	END IF;

	-- 登録レコードのデフォルト
	rCurrData := NULL;
	rCurrData.tuner_event_id = rTunerEvent.id;
	rCurrData.region_id := rTunerEvent.region_id;
	rCurrData.paneler_id := rTunerEvent.paneler_id;
	rCurrData.base_unit_id := rTunerEvent.base_unit_id;
	rCurrData.started_at := rTunerEvent.occurred_at;
	rCurrData.started_usec := rTunerEvent.usec;
	rCurrData.ended_at := timestamp 'infinity';
	rCurrData.channel_id := rTunerEvent.channel_id;
	rCurrData.created_at := current_timestamp;
	rCurrData.updated_at := current_timestamp;

	-- 未来tuner_eventによるended_atの変更
	select
		* into rTmpEvent
	from tuner_events
	where paneler_id = rTunerEvent.paneler_id
	  and occurred_at > rTunerEvent.occurred_at
	  and processed = 1
	order by occurred_at asc
	limit 1
	;
	IF FOUND THEN
		-- ended_atをセット
		rCurrData.ended_at := rTmpEvent.occurred_at;
	END IF;

	-- 直前audience_dataのended_atを更新
	select
		* into rTmpData
	from audience_data
	where paneler_id = rTunerEvent.paneler_id
	  and started_at < rTunerEvent.occurred_at
	  and ended_at > rTunerEvent.occurred_at
	order by started_at desc
	limit 1
	;
	IF FOUND THEN
		-- 直前のaudience_dataのended_atを更新
		IF rTunerEvent.channel_id <= 0 THEN
			update audience_data set ended_at = rTunerEvent.occurred_at, updated_at = current_timestamp where tuner_event_id = rTmpData.tuner_event_id and base_unit_id = rTunerEvent.base_unit_id;
		ELSE
			update audience_data set ended_at = rTunerEvent.occurred_at, updated_at = current_timestamp where tuner_event_id = rTmpData.tuner_event_id;
		END IF;
		RETURN NEXT format('%s update audience_data{%s,%s,%s}', vMsgPrefix, rTmpData.tuner_event_id, rTmpData.paneler_id, rTmpData.started_at);
	END IF;

	-- チャンネルが有効値の場合のみ登録
	IF rCurrData.channel_id > 0 AND rCurrData.channel_id != 99999 THEN
		insert into audience_data select rCurrData.*;
		RETURN NEXT format('%s insert', vMsgPrefix);
	ELSE
		RETURN NEXT format('%s trash(%sch)', vMsgPrefix, rCurrData.channel_id);
	END IF;

	RETURN;
END;
$$;


ALTER FUNCTION public.process_tuner_event(rtunerevent public.tuner_events) OWNER TO switch;

--
-- Name: process_unprocessed_tuner_events(timestamp without time zone, timestamp without time zone); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.process_unprocessed_tuner_events(ptermfrom timestamp without time zone, ptermto timestamp without time zone) RETURNS SETOF text
    LANGUAGE plpgsql
    AS $$
DECLARE
rTunerEvent tuner_events%ROWTYPE;
vCnt integer;
BEGIN

RETURN NEXT format('%s ストアド処理 process_unprocessed_tuner_events を開始します。', clock_timestamp()::timestamp(0));

vCnt := 0;

--対象イベント取得＆ループ
FOR rTunerEvent IN
select
*
from tuner_events
where processed = 0
  and occurred_at >= pTermFrom
  and occurred_at < pTermTo
order by paneler_id asc, occurred_at asc, usec asc, id asc
LOOP

vCnt := vCnt + 1;
IF vCnt = 1 THEN
RETURN NEXT format('%s START EVENT LOOP', clock_timestamp()::timestamp(0));
ELSIF mod(vCnt, 100) = 0 THEN
RETURN NEXT format('%s %s', clock_timestamp()::timestamp(0), vCnt);
END IF;

--登録処理
RETURN QUERY
select process_tuner_event(rTunerEvent);

END LOOP;

IF vCnt > 0 THEN
RETURN NEXT format('%s %s', clock_timestamp()::timestamp(0), vCnt);
END IF;
RETURN NEXT format('%s ストアド処理 process_unprocessed_tuner_events を終了します。', clock_timestamp()::timestamp(0));
RETURN NEXT format('%s件処理しました。', vCnt);

RETURN;
END;
$$;


ALTER FUNCTION public.process_unprocessed_tuner_events(ptermfrom timestamp without time zone, ptermto timestamp without time zone) OWNER TO switch;

--
-- Name: query_cm_viewing_rate_from_work(integer, timestamp without time zone, integer, character varying); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_cm_viewing_rate_from_work(pregionid integer, pframetime timestamp without time zone, pchannelid integer, pdivision character varying) RETURNS TABLE(region_id integer, term_from timestamp without time zone, term_to timestamp without time zone, channel_id integer, division character varying, code character varying, viewing_seconds bigint, viewing_rate real)
    LANGUAGE plpgsql
    AS $_$
DECLARE
vQuery text;
BEGIN
--パラメータチェック
IF pRegionId IS NULL THEN
RAISE EXCEPTION 'パラメータpRegionIdがnullです。';
END IF;
IF pFrameTime IS NULL THEN
RAISE EXCEPTION 'パラメータpFrameTimeがnullです。';
END IF;
IF pChannelId IS NULL THEN
RAISE EXCEPTION 'パラメータpChannelIdがnullです。';
END IF;
IF pDivision IS NULL THEN
RAISE EXCEPTION 'パラメータpDivisionがnullです。';
END IF;

--クエリの組み立て
vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || '   $1 as region_id,';
vQuery := vQuery || '   $2 as term_from,';
vQuery := vQuery || '   $2 + interval ''1 second'' as term_to,';
vQuery := vQuery || '   $3 as channel_id,';
vQuery := vQuery || '   $4 as division,';
vQuery := vQuery || '   q1.code::varchar,';
vQuery := vQuery || '   q1.viewing_number::bigint,';
vQuery := vQuery || '   (q1.viewing_number::numeric / NULLIF(tn.number, 0) * 100)::real as viewing_rate';
vQuery := vQuery || ' from (';
vQuery := vQuery || '   select';
vQuery := vQuery || '     q1.time_box_id,';
vQuery := vQuery || '     q1.code,';
IF pDivision = 'household' THEN
vQuery := vQuery || '     count(distinct q1.household_id) as viewing_number';
ELSE
vQuery := vQuery || '     count(distinct q1.paneler_id) as viewing_number';
END IF;
vQuery := vQuery || '   from (';
vQuery := vQuery || '     select';
vQuery := vQuery || '       w.time_box_id,';
vQuery := vQuery || '       w.paneler_id,';
vQuery := vQuery || '       tp.household_id,';
vQuery := vQuery || '       tp.code';
vQuery := vQuery || '     from cm_report_work w';
vQuery := vQuery || '     join query_panelers_with_attr($1, $2, null, $4) tp';
vQuery := vQuery || '       on tp.time_box_id = w.time_box_id and tp.paneler_id = w.paneler_id';
vQuery := vQuery || '     where w.region_id = $1';
vQuery := vQuery || '       and w.frame_time = $2';
vQuery := vQuery || '       and w.channel_id = $3';
vQuery := vQuery || '   ) q1';
vQuery := vQuery || '   where q1.code is not null';
vQuery := vQuery || '   group by q1.time_box_id, q1.code';
vQuery := vQuery || ' ) q1';
vQuery := vQuery || ' left join time_box_attr_numbers tn on tn.time_box_id = q1.time_box_id and tn.division = $4 and tn.code = q1.code';
--RAISE NOTICE '%', vQuery;

--実行＆返却
RETURN QUERY
EXECUTE vQuery
USING pRegionId, pFrameTime, pChannelId, pDivision
;
RETURN;
END;
$_$;


ALTER FUNCTION public.query_cm_viewing_rate_from_work(pregionid integer, pframetime timestamp without time zone, pchannelid integer, pdivision character varying) OWNER TO switch;

--
-- Name: query_converted_channels(integer, text); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_converted_channels(pchannelid integer DEFAULT NULL::integer, pchannelconversionmode text DEFAULT NULL::text) RETURNS TABLE(base_channel_id integer, channel_id integer)
    LANGUAGE plpgsql
    AS $$
BEGIN

--全局
IF pChannelConversionMode = 'every' OR pChannelConversionMode = 'whole' THEN
RETURN QUERY select id as base_channel_id, -10 as channel_id from channels;
END IF;

--放送種別
IF pChannelConversionMode = 'every' OR pChannelConversionMode = 'type' THEN
RETURN QUERY select id as base_channel_id, case type when 'dt' then -11 when 'bs' then -12 end as channel_id from channels where type in ('dt', 'bs');
END IF;

--通常
IF pChannelConversionMode IS NULL OR pChannelConversionMode NOT IN ('whole', 'type') THEN
IF pChannelId IS NOT NULL THEN
RETURN QUERY select id as base_channel_id, id as channel_id from channels where id = pChannelId;
ELSE
RETURN QUERY select id as base_channel_id, id as channel_id from channels;
END IF;
END IF;

RETURN;
END;
$$;


ALTER FUNCTION public.query_converted_channels(pchannelid integer, pchannelconversionmode text) OWNER TO switch;

--
-- Name: query_converted_channels_for_timeshift(integer, text); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_converted_channels_for_timeshift(pchannelid integer DEFAULT NULL::integer, pchannelconversionmode text DEFAULT NULL::text) RETURNS TABLE(base_channel_id integer, channel_id integer)
    LANGUAGE plpgsql
    AS $$
BEGIN

--全局
IF pChannelConversionMode = 'every' OR pChannelConversionMode = 'whole' THEN
RETURN QUERY select id as base_channel_id, -10 as channel_id from channels where ts_flag = 1;
END IF;

--放送種別
IF pChannelConversionMode = 'every' OR pChannelConversionMode = 'type' THEN
RETURN QUERY select id as base_channel_id, case type when 'dt' then -11 when 'bs' then -12 end as channel_id from channels where type in ('dt', 'bs') and ts_flag = 1;
END IF;

--通常
IF pChannelConversionMode IS NULL OR pChannelConversionMode NOT IN ('whole', 'type') THEN
IF pChannelId IS NOT NULL THEN
RETURN QUERY select id as base_channel_id, id as channel_id from channels where id = pChannelId and ts_flag = 1;
ELSE
RETURN QUERY select id as base_channel_id, id as channel_id from channels where ts_flag = 1;
END IF;
END IF;

RETURN;
END;
$$;


ALTER FUNCTION public.query_converted_channels_for_timeshift(pchannelid integer, pchannelconversionmode text) OWNER TO switch;

--
-- Name: query_household_viewing_data(integer, timestamp without time zone, timestamp without time zone, integer, interval, text); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_household_viewing_data(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pchannelid integer DEFAULT NULL::integer, psplitinterval interval DEFAULT NULL::interval, pchannelconversionmode text DEFAULT NULL::text) RETURNS TABLE(time_box_id integer, term_from timestamp without time zone, term_to timestamp without time zone, household_id integer, started_at timestamp without time zone, ended_at timestamp without time zone, channel_id integer, viewing_seconds integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
--パラメータチェック
IF pRegionId IS NULL THEN
RAISE EXCEPTION 'パラメータpRegionIdがnullです。';
END IF;
IF pTermFrom IS NULL THEN
RAISE EXCEPTION 'パラメータpTermFromがnullです。';
END IF;
IF pTermTo IS NULL THEN
RAISE EXCEPTION 'パラメータpTermToがnullです。';
END IF;

IF pSplitInterval IS NULL THEN
pSplitInterval := pTermTo - pTermFrom;
END IF;

--実行＆返却
RETURN QUERY
select
a.time_box_id,
a.term_from,
a.term_to,
a.household_id,
min(a.started_at) as started_at,
max(a.ended_at) as ended_at,
a.channel_id,
extract(epoch from (max(a.ended_at) - min(a.started_at)))::integer as viewing_seconds
from (
select
a.*,
sum(a.top_flag) over(partition by a.household_id, a.channel_id order by a.started_at asc, a.ended_at asc) as seq
from (
select
a.*,
case when a.started_at <= max(a.ended_at) over(partition by a.time_box_id, a.household_id, a.channel_id order by a.started_at asc, a.ended_at asc rows between unbounded preceding and 1 preceding) then 0 else 1 end as top_flag
from (
select
tb.id as time_box_id,
h.term_from,
h.term_to,
tp.household_id,
greatest(ad.started_at, tb.started_at, h.term_from, pTermFrom) as started_at,
least(ad.ended_at, tb.ended_at, h.term_to, pTermTo) as ended_at,
cc.channel_id
from audience_data ad
join (
select
h.h as term_from,
h.h + pSplitInterval as term_to
from generate_series (
pTermFrom,
pTermTo - interval '1 second',
pSplitInterval
) h
) h on ad.started_at < h.term_to and ad.ended_at > h.term_from
join time_boxes tb on tb.region_id = pRegionId and tb.started_at < pTermTo and tb.ended_at > pTermFrom
and tb.started_at < ad.ended_at and tb.ended_at > ad.started_at
join time_box_panelers tp on tp.time_box_id = tb.id and tp.paneler_id = ad.paneler_id
join time_box_channels tc on tc.time_box_id = tb.id and tc.channel_id = ad.channel_id
join query_converted_channels(pChannelId, pChannelConversionMode) cc on cc.base_channel_id = ad.channel_id
where ad.region_id = pRegionId and ad.started_at < pTermTo and ad.ended_at > pTermFrom
) a
) a
) a
group by a.time_box_id, a.term_from, a.term_to, a.household_id, a.channel_id, a.seq
;
RETURN;
END;
$$;


ALTER FUNCTION public.query_household_viewing_data(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pchannelid integer, psplitinterval interval, pchannelconversionmode text) OWNER TO switch;

--
-- Name: query_panelers_with_attr(integer, character varying, boolean); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_panelers_with_attr(ptimeboxid integer, pdivision character varying, puniquemode boolean DEFAULT false) RETURNS TABLE(time_box_id integer, paneler_id integer, household_id integer, division character varying, code character varying)
    LANGUAGE plpgsql
    AS $_$
DECLARE
rTimeBox time_boxes%ROWTYPE;
rAttrDivDef attr_divs%ROWTYPE;
cAttrDivCode refcursor;
rAttrDivCode attr_divs%ROWTYPE;
vQuery text;
BEGIN
--パラメータチェック
IF pTimeBoxId IS NULL THEN
RAISE EXCEPTION 'パラメータpTimeBoxIdがnullです。';
END IF;
IF pDivision IS NULL THEN
RAISE EXCEPTION 'パラメータpDivisionがnullです。';
END IF;
--pUniqueModeの補正
IF pUniqueMode IS NULL THEN
pUniqueMode := FALSE;
END IF;

--タイムボックスの取得
select * into rTimeBox from time_boxes tb where tb.id = pTimeBoxId;
--存在チェック
IF NOT FOUND THEN
RAISE EXCEPTION 'タイムボックスが見つかりません。 pTimeBoxId=%', pTimeBoxId;
END IF;

--個人／世帯のときの特例処理
IF pDivision IN ('personal', 'household') THEN
vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || '   tp.time_box_id,';
vQuery := vQuery || '   tp.paneler_id,';
vQuery := vQuery || '   tp.household_id,';
vQuery := vQuery || '   $2 as division,';
vQuery := vQuery || '   ''1''::varchar as code';
vQuery := vQuery || ' from time_box_panelers tp';
vQuery := vQuery || ' where tp.time_box_id = $1';

--RAISE NOTICE '%', vQuery;
RETURN QUERY EXECUTE vQuery USING pTimeBoxId, pDivision;
--終了
RETURN;
END IF;

--属性区分定義の取得
select * into rAttrDivDef from attr_divs ad where ad.division = pDivision and ad.code = '_def';
--存在チェック
IF NOT FOUND THEN
RAISE EXCEPTION '属性定義が見つかりません。 division=%',  pDivision;
END IF;

--属性区分コードのカーソルを開く
OPEN cAttrDivCode FOR
select
ad.*
from attr_divs ad
where ad.division = pDivision
  and ad.code <> '_def'
  and ad.definition is not null
order by ad.display_order asc, ad.code asc
;

IF NOT pUniqueMode THEN
--重複許容の場合、codeごとにselectで取得する
LOOP
FETCH cAttrDivCode INTO rAttrDivCode;
IF NOT FOUND THEN
EXIT;
END IF;

vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || '   tp.time_box_id,';
vQuery := vQuery || '   tp.paneler_id,';
vQuery := vQuery || '   tp.household_id,';
vQuery := vQuery || '   $2 as division,';
vQuery := vQuery || '   $3 as code';
vQuery := vQuery || ' from time_box_panelers tp';
vQuery := vQuery || ' where tp.time_box_id = $1';
vQuery := vQuery || '   and ' || build_attr_condition(rAttrDivCode.definition, 'tp');

--RAISE NOTICE '%', vQuery;
RETURN QUERY EXECUTE vQuery USING pTimeBoxId, pDivision, rAttrDivCode.code;

END LOOP;
END IF;

MOVE ABSOLUTE 0 FROM cAttrDivCode;

--1回のselectでデータ取得（重複許容の場合は未分類パネル、重複不可の場合は対象パネル）
vQuery := '';
vQuery := vQuery || ' select * from (';
vQuery := vQuery || '   select';
vQuery := vQuery || '     tp.time_box_id,';
vQuery := vQuery || '     tp.paneler_id,';
vQuery := vQuery || '     tp.household_id,';
vQuery := vQuery || '     $2 as division,';
vQuery := vQuery || '     (case when 1 = 0 then ''dummy''';
LOOP
FETCH cAttrDivCode INTO rAttrDivCode;
IF NOT FOUND THEN
EXIT;
END IF;
vQuery := vQuery || ' when ' || build_attr_condition(rAttrDivCode.definition, 'tp') || ' then ''' || rAttrDivCode.code || '''';
END LOOP;
vQuery := vQuery || '     else null end)::varchar as code';
vQuery := vQuery || '   from time_box_panelers tp';
vQuery := vQuery || '   where tp.time_box_id = $1';
vQuery := vQuery || ' ) q';
IF NOT pUniqueMode THEN
--重複許容の場合、未分類パネルに絞る
vQuery := vQuery || ' where q.code is null';
END IF;

--RAISE NOTICE '%', vQuery;
RETURN QUERY EXECUTE vQuery USING pTimeBoxId, pDivision;

CLOSE cAttrDivCode;

RETURN;
END;
$_$;


ALTER FUNCTION public.query_panelers_with_attr(ptimeboxid integer, pdivision character varying, puniquemode boolean) OWNER TO switch;

--
-- Name: query_panelers_with_attr(integer, timestamp without time zone, timestamp without time zone, character varying, boolean); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_panelers_with_attr(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pdivision character varying, puniquemode boolean DEFAULT false) RETURNS TABLE(time_box_id integer, paneler_id integer, household_id integer, division character varying, code character varying)
    LANGUAGE plpgsql
    AS $$
DECLARE
rTimeBox time_boxes%ROWTYPE;
BEGIN
--パラメータチェック
IF pRegionId IS NULL THEN
RAISE EXCEPTION 'パラメータpRegionIdがnullです。';
END IF;
IF pTermFrom IS NULL THEN
RAISE EXCEPTION 'パラメータpTermFromがnullです。';
END IF;
--パラメータ補正
IF pTermTo IS NULL THEN
pTermTo := pTermFrom + interval '1 second';
END IF;

--タイムボックスの取得
FOR rTimeBox IN
select * from time_boxes tb
where tb.region_id = pRegionId and tb.started_at < pTermTo and tb.ended_at > pTermFrom
order by tb.started_at
LOOP
RETURN QUERY select * from query_panelers_with_attr(rTimeBox.id, pDivision, pUniqueMode);
END LOOP;

RETURN;
END;
$$;


ALTER FUNCTION public.query_panelers_with_attr(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pdivision character varying, puniquemode boolean) OWNER TO switch;

--
-- Name: query_panelers_with_attr_for_timeshift(integer, character varying, integer); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_panelers_with_attr_for_timeshift(ptimeboxid integer, pdivision character varying, pnexttimeboxid integer DEFAULT 0) RETURNS TABLE(time_box_id integer, paneler_id integer, household_id integer, division character varying, code character varying)
    LANGUAGE plpgsql
    AS $_$
DECLARE
rTimeBox time_boxes%ROWTYPE;
rNextTimeBox time_boxes%ROWTYPE;
rAttrDivDef attr_divs%ROWTYPE;
cAttrDivCode refcursor;
rAttrDivCode attr_divs%ROWTYPE;
vQuery text;
BEGIN
--パラメータチェック
IF pTimeBoxId IS NULL THEN
RAISE EXCEPTION 'パラメータpTimeBoxIdがnullです。';
END IF;
IF pDivision IS NULL THEN
RAISE EXCEPTION 'パラメータpDivisionがnullです。';
END IF;

--タイムボックスの取得
select * into rTimeBox from time_boxes tb where tb.id = pTimeBoxId;
--存在チェック
IF NOT FOUND THEN
RAISE EXCEPTION 'タイムボックスが見つかりません。 pTimeBoxId=%', pTimeBoxId;
END IF;

--次回タイムボックスの取得
IF pNextTimeBoxId > 0 THEN
select * into rNextTimeBox from time_boxes where id = pNextTimeBoxId;
--存在チェック
IF NOT FOUND THEN
RAISE EXCEPTION 'タイムボックスが見つかりません。 pNextTimeBoxId=%', pNextTimeBoxId;
END IF;
END IF;


--個人／世帯のときの特例処理
IF pDivision IN ('personal', 'household') THEN
vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || '   tp.time_box_id,';
vQuery := vQuery || '   tp.paneler_id,';
vQuery := vQuery || '   tp.household_id,';
vQuery := vQuery || '   $3 as division,';
vQuery := vQuery || '   ''1''::varchar as code';
vQuery := vQuery || ' from (';
IF pNextTimeBoxId = 0 THEN
vQuery := vQuery || '       select ';
vQuery := vQuery || '         tbp.time_box_id,';
vQuery := vQuery || '         tbp.paneler_id,';
vQuery := vQuery || '         tbp.household_id';
vQuery := vQuery || '       from time_box_panelers tbp';
vQuery := vQuery || '       where tbp.time_box_id = $1';
ELSE
vQuery := vQuery || '       select ';
vQuery := vQuery || '         tbp.time_box_id,';
vQuery := vQuery || '         tbp.paneler_id,';
vQuery := vQuery || '         tbp.household_id';
vQuery := vQuery || '       from time_box_panelers tbp';
vQuery := vQuery || '       join time_box_panelers prev_tbp on tbp.paneler_id = prev_tbp.paneler_id';
vQuery := vQuery || '         and prev_tbp.time_box_id = $1';
vQuery := vQuery || '       where tbp.time_box_id = $2';
END IF;
vQuery := vQuery || ' ) tp';

--RAISE NOTICE '%', vQuery;
RETURN QUERY EXECUTE vQuery USING pTimeBoxId, pNextTimeBoxId ,pDivision;
--終了
RETURN;
END IF;

--属性区分定義の取得
select * into rAttrDivDef from attr_divs ad where ad.division = pDivision and ad.code = '_def';
--存在チェック
IF NOT FOUND THEN
RAISE EXCEPTION '属性定義が見つかりません。 division=%',  pDivision;
END IF;

--属性区分コードのカーソルを開く
OPEN cAttrDivCode FOR
select
  *
from attr_divs ad
where ad.division = pDivision
  and ad.code <> '_def'
  and ad.definition is not null
order by ad.display_order asc, ad.code asc
;

LOOP
FETCH cAttrDivCode INTO rAttrDivCode;
IF NOT FOUND THEN
EXIT;
END IF;

vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || '   tp.time_box_id,';
vQuery := vQuery || '   tp.paneler_id,';
vQuery := vQuery || '   tp.household_id,';
vQuery := vQuery || '   $3 as division,';
vQuery := vQuery || '   $4 as code';
vQuery := vQuery || ' from (';
IF pNextTimeBoxId = 0 THEN
vQuery := vQuery || '       select ';
vQuery := vQuery || '         tbp.time_box_id,';
vQuery := vQuery || '         tbp.paneler_id,';
vQuery := vQuery || '         tbp.household_id';
vQuery := vQuery || '       from time_box_panelers tbp';
vQuery := vQuery || '       where tbp.time_box_id = $1';
vQuery := vQuery || '       and ' || build_attr_condition(rAttrDivCode.definition, 'tbp');
ELSE
vQuery := vQuery || '       select ';
vQuery := vQuery || '         tbp.time_box_id,';
vQuery := vQuery || '         tbp.paneler_id,';
vQuery := vQuery || '         tbp.household_id';
vQuery := vQuery || '       from time_box_panelers tbp';
vQuery := vQuery || '       join time_box_panelers prev_tbp on tbp.paneler_id = prev_tbp.paneler_id';
vQuery := vQuery || '         and prev_tbp.time_box_id = $1';
vQuery := vQuery || '       where tbp.time_box_id = $2';
vQuery := vQuery || '       and ' || build_attr_condition(rAttrDivCode.definition, 'tbp');
END IF;
vQuery := vQuery || ' ) tp';

--RAISE NOTICE '%', vQuery;
RETURN QUERY EXECUTE vQuery USING pTimeBoxId, pNextTimeBoxId ,pDivision, rAttrDivCode.code;

END LOOP;

CLOSE cAttrDivCode;

RETURN;
END;
$_$;


ALTER FUNCTION public.query_panelers_with_attr_for_timeshift(ptimeboxid integer, pdivision character varying, pnexttimeboxid integer) OWNER TO switch;

--
-- Name: query_personal_viewing_data(integer, timestamp without time zone, timestamp without time zone, integer, interval, text); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_personal_viewing_data(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pchannelid integer DEFAULT NULL::integer, psplitinterval interval DEFAULT NULL::interval, pchannelconversionmode text DEFAULT NULL::text) RETURNS TABLE(time_box_id integer, term_from timestamp without time zone, term_to timestamp without time zone, paneler_id integer, base_unit_id integer, household_id integer, started_at timestamp without time zone, ended_at timestamp without time zone, channel_id integer, viewing_seconds integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
--パラメータチェック
IF pRegionId IS NULL THEN
RAISE EXCEPTION 'パラメータpRegionIdがnullです。';
END IF;
IF pTermFrom IS NULL THEN
RAISE EXCEPTION 'パラメータpTermFromがnullです。';
END IF;
IF pTermTo IS NULL THEN
RAISE EXCEPTION 'パラメータpTermToがnullです。';
END IF;

IF pSplitInterval IS NULL THEN
pSplitInterval := pTermTo - pTermFrom;
END IF;

--実行＆返却
RETURN QUERY
select
a.time_box_id,
a.term_from,
a.term_to,
a.paneler_id,
a.base_unit_id,
a.household_id,
a.started_at,
a.ended_at,
a.channel_id,
extract(epoch from (a.ended_at - a.started_at))::integer as viewing_seconds
from (
select
tb.id as time_box_id,
h.term_from,
h.term_to,
ad.paneler_id,
ad.base_unit_id,
tp.household_id,
greatest(ad.started_at, tb.started_at, h.term_from, pTermFrom) as started_at,
least(ad.ended_at, tb.ended_at, h.term_to, pTermTo) as ended_at,
cc.channel_id
from audience_data ad
join (
select
h.h as term_from,
h.h + pSplitInterval as term_to
from generate_series (
pTermFrom,
pTermTo - interval '1 second',
pSplitInterval
) h
) h on ad.started_at < h.term_to and ad.ended_at > h.term_from
join time_boxes tb on tb.region_id = pRegionId and tb.started_at < pTermTo and tb.ended_at > pTermFrom
and tb.started_at < ad.ended_at and tb.ended_at > ad.started_at
join time_box_panelers tp on tp.time_box_id = tb.id and tp.paneler_id = ad.paneler_id
join time_box_channels tc on tc.time_box_id = tb.id and tc.channel_id = ad.channel_id
join query_converted_channels(pChannelId, pChannelConversionMode) cc on cc.base_channel_id = ad.channel_id
where ad.region_id = pRegionId and ad.started_at < pTermTo and ad.ended_at > pTermFrom
) a
;
RETURN;
END;
$$;


ALTER FUNCTION public.query_personal_viewing_data(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pchannelid integer, psplitinterval interval, pchannelconversionmode text) OWNER TO switch;

--
-- Name: query_personal_viewing_data_with_attr(integer, timestamp without time zone, timestamp without time zone, character varying, integer, interval, text); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_personal_viewing_data_with_attr(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pdivision character varying, pchannelid integer DEFAULT NULL::integer, psplitinterval interval DEFAULT NULL::interval, pchannelconversionmode text DEFAULT NULL::text) RETURNS TABLE(time_box_id integer, term_from timestamp without time zone, term_to timestamp without time zone, paneler_id integer, division character varying, code character varying, base_unit_id integer, household_id integer, started_at timestamp without time zone, ended_at timestamp without time zone, channel_id integer, viewing_seconds integer)
    LANGUAGE plpgsql
    AS $_$
DECLARE
vQuery text;
BEGIN
--パラメータチェック
IF pRegionId IS NULL THEN
RAISE EXCEPTION 'パラメータpRegionIdがnullです。';
END IF;
IF pTermFrom IS NULL THEN
RAISE EXCEPTION 'パラメータpTermFromがnullです。';
END IF;
IF pTermTo IS NULL THEN
RAISE EXCEPTION 'パラメータpTermToがnullです。';
END IF;
IF pDivision IS NULL THEN
RAISE EXCEPTION 'パラメータpDivisionがnullです。';
END IF;

--クエリの組み立て
vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || '   v.time_box_id,';
vQuery := vQuery || '   v.term_from,';
vQuery := vQuery || '   v.term_to,';
vQuery := vQuery || '   v.paneler_id,';
vQuery := vQuery || '   $4 as division,';
vQuery := vQuery || '   tp.code,';
vQuery := vQuery || '   v.base_unit_id,';
vQuery := vQuery || '   v.household_id,';
vQuery := vQuery || '   v.started_at,';
vQuery := vQuery || '   v.ended_at,';
vQuery := vQuery || '   v.channel_id,';
vQuery := vQuery || '   v.viewing_seconds';
vQuery := vQuery || ' from query_personal_viewing_data($1, $2, $3, $5, $6, $7) v';
vQuery := vQuery || ' left join query_panelers_with_attr($1, $2, $3, $4) tp';
vQuery := vQuery || '   on tp.time_box_id = v.time_box_id and tp.paneler_id = v.paneler_id';
--RAISE NOTICE '%', vQuery;

--実行＆返却
RETURN QUERY
EXECUTE vQuery
USING pRegionId, pTermFrom, pTermTo, pDivision, pChannelId, pSplitInterval, pChannelConversionMode
;
RETURN;
END;
$_$;


ALTER FUNCTION public.query_personal_viewing_data_with_attr(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pdivision character varying, pchannelid integer, psplitinterval interval, pchannelconversionmode text) OWNER TO switch;

--
-- Name: query_samples(integer, character varying); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_samples(ptimeboxid integer, pdivision character varying) RETURNS TABLE(time_box_id integer, division character varying, code character varying, number integer)
    LANGUAGE plpgsql
    AS $_$
DECLARE
rTimeBox time_boxes%ROWTYPE;
vQuery text;
BEGIN
--パラメータチェック
IF pTimeBoxId IS NULL THEN
RAISE EXCEPTION 'パラメータpRegionIdがnullです。';
END IF;
IF pDivision IS NULL THEN
RAISE EXCEPTION 'パラメータpDivisionがnullです。';
END IF;

--タイムボックスの取得
select * into rTimeBox from time_boxes where id = pTimeBoxId;
--存在チェック
IF NOT FOUND THEN
RAISE EXCEPTION 'タイムボックスが見つかりません。 pTimeBoxId=%', pTimeBoxId;
END IF;

--個人の場合
IF pDivision = 'personal' THEN
--実行＆返却
RETURN QUERY
select
pTimeBoxId as time_box_id,
'personal'::varchar as division,
'1'::varchar as code,
count(*)::integer as samples
from time_box_panelers p
where p.time_box_id = pTimeBoxId
;
RETURN;
END IF;

--世帯の場合
IF pDivision = 'household' THEN
--実行＆返却
RETURN QUERY
select
pTimeBoxId as time_box_id,
'household'::varchar as division,
'1'::varchar as code,
count(distinct p.household_id)::integer as samples
from time_box_panelers p
where p.time_box_id = pTimeBoxId
;
RETURN;
END IF;

--属性区分の場合は、クエリを組み立てる
vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || '   $1 as time_box_id,';
vQuery := vQuery || '   $2 as division,';
vQuery := vQuery || '   d.code as code,';
vQuery := vQuery || '   coalesce(res.samples, 0)::integer as number';
vQuery := vQuery || ' from attr_divs d';
vQuery := vQuery || ' left join (';
vQuery := vQuery || '   select';
vQuery := vQuery || '     p.code as code,';
vQuery := vQuery || '     count(*) as samples';
vQuery := vQuery || '   from query_panelers_with_attr($1, $2) p';
vQuery := vQuery || '   where p.code is not null';
vQuery := vQuery || '   group by p.code';
vQuery := vQuery || ' ) res on d.code = res.code';
vQuery := vQuery || ' where d.division = $2 and d.code <> ''_def''';
vQuery := vQuery || ' order by d.display_order asc';
-- RAISE NOTICE '%', vQuery;
--実行＆返却
RETURN QUERY
EXECUTE vQuery
USING pTimeBoxId, pDivision
;
RETURN;
END;
$_$;


ALTER FUNCTION public.query_samples(ptimeboxid integer, pdivision character varying) OWNER TO switch;

--
-- Name: query_samples_for_timeshift(integer, character varying, integer); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_samples_for_timeshift(ptimeboxid integer, pdivision character varying, pnexttimeboxid integer DEFAULT 0) RETURNS TABLE(time_box_id integer, division character varying, code character varying, number integer)
    LANGUAGE plpgsql
    AS $_$
DECLARE
rTimeBox time_boxes%ROWTYPE;
rNextTimeBox time_boxes%ROWTYPE;
cAttrDivCode refcursor;
rAttrDivCode attr_divs%ROWTYPE;
vQuery text;
BEGIN
--パラメータチェック
IF pTimeBoxId IS NULL THEN
RAISE EXCEPTION 'パラメータpTimeBoxIdがnullです。';
END IF;

IF pDivision IS NULL THEN
RAISE EXCEPTION 'パラメータpDivisionがnullです。';
END IF;

--タイムボックスの取得
select * into rTimeBox from time_boxes where id = pTimeBoxId;
--存在チェック
IF NOT FOUND THEN
RAISE EXCEPTION 'タイムボックスが見つかりません。 pTimeBoxId=%', pTimeBoxId;
END IF;

--次回タイムボックスの取得
IF pNextTimeBoxId > 0 THEN
select * into rNextTimeBox from time_boxes where id = pNextTimeBoxId;
--存在チェック
IF NOT FOUND THEN
RAISE EXCEPTION 'タイムボックスが見つかりません。 pNextTimeBoxId=%', pNextTimeBoxId;
END IF;
END IF;

--個人の場合
IF pDivision = 'personal' THEN
--実行＆返却
vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || ' $1 as time_box_id,';
vQuery := vQuery || ' ''personal''::varchar as division,';
vQuery := vQuery || ' ''1''::varchar as code,';
vQuery := vQuery || ' count(*)::integer as samples';
vQuery := vQuery || ' from (';
IF pNextTimeBoxId = 0 THEN
vQuery := vQuery || '       select ';
vQuery := vQuery || '         tbp.time_box_id,';
vQuery := vQuery || '         tbp.paneler_id,';
vQuery := vQuery || '         tbp.household_id';
vQuery := vQuery || '       from time_box_panelers tbp';
vQuery := vQuery || '       where tbp.time_box_id = $1';
ELSE
vQuery := vQuery || '       select ';
vQuery := vQuery || '         tbp.time_box_id,';
vQuery := vQuery || '         tbp.paneler_id,';
vQuery := vQuery || '         tbp.household_id';
vQuery := vQuery || '       from time_box_panelers tbp';
vQuery := vQuery || '       join time_box_panelers prev_tbp on tbp.paneler_id = prev_tbp.paneler_id';
vQuery := vQuery || '         and prev_tbp.time_box_id = $1';
vQuery := vQuery || '       where tbp.time_box_id = $2';
END IF;
vQuery := vQuery || ' ) tp';

RETURN QUERY EXECUTE vQuery USING pTimeBoxId, pNextTimeBoxId;
END IF;

--世帯の場合
IF pDivision = 'household' THEN
--実行＆返却
vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || ' $1 as time_box_id,';
vQuery := vQuery || ' ''household''::varchar as division,';
vQuery := vQuery || ' ''1''::varchar as code,';
vQuery := vQuery || ' count(distinct tp.household_id)::integer as samples';
vQuery := vQuery || ' from (';
IF pNextTimeBoxId = 0 THEN
vQuery := vQuery || '       select ';
vQuery := vQuery || '         tbp.time_box_id,';
vQuery := vQuery || '         tbp.paneler_id,';
vQuery := vQuery || '         tbp.household_id';
vQuery := vQuery || '       from time_box_panelers tbp';
vQuery := vQuery || '       where tbp.time_box_id = $1';
ELSE
vQuery := vQuery || '       select ';
vQuery := vQuery || '         tbp.time_box_id,';
vQuery := vQuery || '         tbp.paneler_id,';
vQuery := vQuery || '         tbp.household_id';
vQuery := vQuery || '       from time_box_panelers tbp';
vQuery := vQuery || '       join time_box_panelers prev_tbp on tbp.paneler_id = prev_tbp.paneler_id';
vQuery := vQuery || '         and prev_tbp.time_box_id = $1';
vQuery := vQuery || '       where tbp.time_box_id = $2';
END IF;
vQuery := vQuery || ' ) tp';

RETURN QUERY EXECUTE vQuery USING pTimeBoxId, pNextTimeBoxId;
END IF;

--属性区分コードのカーソルを開く
OPEN cAttrDivCode FOR
select
ad.*
from attr_divs ad
where ad.division = pDivision
  and ad.code <> '_def'
  and ad.definition is not null
order by ad.display_order asc, ad.code asc
;

LOOP
FETCH cAttrDivCode INTO rAttrDivCode;
IF NOT FOUND THEN
EXIT;
END IF;

vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || '   $1 as time_box_id,';
vQuery := vQuery || '   $3 as division,';
vQuery := vQuery || '   d.code as code,';
vQuery := vQuery || '   coalesce(res.samples, 0)::integer as number';
vQuery := vQuery || ' from attr_divs d';
vQuery := vQuery || ' join (';
vQuery := vQuery || '   select';
vQuery := vQuery || '     p.code as code,';
vQuery := vQuery || '     count(*) as samples';
vQuery := vQuery || '   from (';
vQuery := vQuery || '     select';
vQuery := vQuery || '       tp.time_box_id,';
vQuery := vQuery || '       tp.paneler_id,';
vQuery := vQuery || '       tp.household_id,';
vQuery := vQuery || '       $3 as division,';
vQuery := vQuery || '       $4 as code';
vQuery := vQuery || '     from (';
IF pNextTimeBoxId = 0 THEN
vQuery := vQuery || '       select ';
vQuery := vQuery || '         tbp.time_box_id,';
vQuery := vQuery || '         tbp.paneler_id,';
vQuery := vQuery || '         tbp.household_id';
vQuery := vQuery || '       from time_box_panelers tbp';
vQuery := vQuery || '       where tbp.time_box_id = $1';
vQuery := vQuery || '       and ' || build_attr_condition(rAttrDivCode.definition, 'tbp');
ELSE
vQuery := vQuery || '       select ';
vQuery := vQuery || '         tbp.time_box_id,';
vQuery := vQuery || '         tbp.paneler_id,';
vQuery := vQuery || '         tbp.household_id';
vQuery := vQuery || '       from time_box_panelers tbp';
vQuery := vQuery || '       join time_box_panelers prev_tbp on tbp.paneler_id = prev_tbp.paneler_id';
vQuery := vQuery || '         and prev_tbp.time_box_id = $1';
vQuery := vQuery || '       where tbp.time_box_id = $2';
vQuery := vQuery || '       and ' || build_attr_condition(rAttrDivCode.definition, 'tbp');
END IF;
vQuery := vQuery || '     ) tp';
vQuery := vQuery || '   ) p';
vQuery := vQuery || '   where p.code is not null';
vQuery := vQuery || '   group by p.code';
vQuery := vQuery || ' ) res on d.code = res.code';
vQuery := vQuery || ' where d.division = $3 and d.code <> ''_def''';
vQuery := vQuery || ' order by d.display_order asc';

--RAISE NOTICE '%', vQuery;
RETURN QUERY EXECUTE vQuery USING pTimeBoxId, pNextTimeBoxId, pDivision, rAttrDivCode.code;

END LOOP;

--属性区分の場合は、クエリを組み立てる
RETURN;
END;
$_$;


ALTER FUNCTION public.query_samples_for_timeshift(ptimeboxid integer, pdivision character varying, pnexttimeboxid integer) OWNER TO switch;

--
-- Name: query_time_box_channels_for_timeshift(integer, integer); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_time_box_channels_for_timeshift(ptimeboxid integer, pnexttimeboxid integer) RETURNS TABLE(time_box_id integer, channel_id integer)
    LANGUAGE plpgsql
    AS $_$
DECLARE
rTimeBox time_boxes%ROWTYPE;
rNextTimeBox time_boxes%ROWTYPE;
vQuery text;
BEGIN
--パラメータチェック
IF pTimeBoxId IS NULL THEN
RAISE EXCEPTION 'パラメータpTimeBoxIdがnullです。';
END IF;

IF pNextTimeBoxId IS NULL THEN
RAISE EXCEPTION 'パラメータpNextTimeBoxIdがnullです。';
END IF;

--タイムボックスの取得
select * into rTimeBox from time_boxes tb where tb.id = pTimeBoxId;
--存在チェック
IF NOT FOUND THEN
RAISE EXCEPTION 'タイムボックスが見つかりません。 pTimeBoxId=%', pTimeBoxId;
END IF;

--次回タイムボックスの取得
IF pNextTimeBoxId > 0 THEN
select * into rNextTimeBox from time_boxes where id = pNextTimeBoxId;
--存在チェック
IF NOT FOUND THEN
RAISE EXCEPTION '次週タイムボックスが見つかりません。 pNextTimeBoxId=%', pNextTimeBoxId;
END IF;
END IF;

vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || '   tc.time_box_id,';
vQuery := vQuery || '   tc.channel_id';
vQuery := vQuery || ' from (';
IF pNextTimeBoxId = 0 THEN
vQuery := vQuery || '       select ';
vQuery := vQuery || '         tbc.time_box_id,';
vQuery := vQuery || '         tbc.channel_id';
vQuery := vQuery || '       from time_box_channels tbc';
vQuery := vQuery || '       where tbc.time_box_id = $1';
ELSE
vQuery := vQuery || '       select ';
vQuery := vQuery || '         tbc.time_box_id,';
vQuery := vQuery || '         tbc.channel_id';
vQuery := vQuery || '       from time_box_channels tbc';
vQuery := vQuery || '       join time_box_channels prev_tbc on tbc.channel_id = prev_tbc.channel_id';
vQuery := vQuery || '         and prev_tbc.time_box_id = $1';
vQuery := vQuery || '       where tbc.time_box_id = $2';
END IF;
vQuery := vQuery || ' ) tc';

--RAISE NOTICE '%', vQuery;
RETURN QUERY EXECUTE vQuery USING pTimeBoxId, pNextTimeBoxId;
--終了
RETURN;
END;
$_$;


ALTER FUNCTION public.query_time_box_channels_for_timeshift(ptimeboxid integer, pnexttimeboxid integer) OWNER TO switch;

--
-- Name: query_viewing_rate(integer, timestamp without time zone, timestamp without time zone, integer, character varying, interval, text); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.query_viewing_rate(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pchannelid integer, pdivision character varying, psplitinterval interval DEFAULT NULL::interval, pchannelconversionmode text DEFAULT NULL::text) RETURNS TABLE(region_id integer, term_from timestamp without time zone, term_to timestamp without time zone, channel_id integer, division character varying, code character varying, viewing_seconds bigint, viewing_rate real)
    LANGUAGE plpgsql
    AS $_$
DECLARE
rTimeBoxSummary RECORD;
vQuery text;
BEGIN
--パラメータチェック
IF pRegionId IS NULL THEN
RAISE EXCEPTION 'パラメータpRegionIdがnullです。';
END IF;
IF pTermFrom IS NULL THEN
RAISE EXCEPTION 'パラメータpTermFromがnullです。';
END IF;
IF pTermTo IS NULL THEN
pTermTo := pTermFrom + interval '1 second';
END IF;
IF pDivision IS NULL THEN
RAISE EXCEPTION 'パラメータpDivisionがnullです。';
END IF;

--対象タイムボックスのチェック
select
count(*) as cnt,
min(tb.started_at) as min_started_at,
max(tb.ended_at) as max_ended_at
into rTimeBoxSummary
from time_boxes tb
where tb.region_id = pRegionId
  and tb.started_at < pTermTo and tb.ended_at > pTermFrom;
--存在チェック
IF rTimeBoxSummary.cnt = 0 THEN
RAISE EXCEPTION '対象のタイムボックスが見つかりません。 pRegionId=%, pTerm=% ～ %', pRegionId, pTermFrom, pTermTo;
END IF;
--pTermFromの範囲チェック
IF pTermFrom < rTimeBoxSummary.min_started_at THEN
RAISE EXCEPTION 'pTermFromが範囲外です。 pTermFrom=%, LIMIT=%', pTermFrom, rTimeBoxSummary.min_started_at;
END IF;
--pTermToの範囲チェック
IF pTermTo > rTimeBoxSummary.max_ended_at THEN
RAISE EXCEPTION 'pTermToが範囲外です。 pTermTo=%, LIMIT=%', pTermTo, rTimeBoxSummary.max_ended_at;
END IF;

--クエリの組み立て
vQuery := '';
vQuery := vQuery || ' select';
vQuery := vQuery || '   $1 as region_id,';
vQuery := vQuery || '   q1.term_from,';
vQuery := vQuery || '   q1.term_to,';
vQuery := vQuery || '   q1.channel_id,';
vQuery := vQuery || '   $5 as division,';
vQuery := vQuery || '   q1.code::varchar,';
vQuery := vQuery || '   sum(q1.viewing_seconds)::bigint as viewing_seconds,';
vQuery := vQuery || '   (sum(q1.viewing_seconds)::numeric / NULLIF(sum(tn.number * least(tbq.seconds, extract(epoch from $6))), 0) * 100)::real as viewing_rate';
vQuery := vQuery || ' from (';
vQuery := vQuery || '   select';
vQuery := vQuery || '     v.term_from,';
vQuery := vQuery || '     v.term_to,';
vQuery := vQuery || '     v.time_box_id,';
vQuery := vQuery || '     v.channel_id,';
vQuery := vQuery || '     v.code,';
vQuery := vQuery || '     sum(v.viewing_seconds)::bigint as viewing_seconds';
vQuery := vQuery || '   from (';
CASE pDivision
WHEN 'personal' THEN
vQuery := vQuery || ' select';
vQuery := vQuery || '   v.*,';
vQuery := vQuery || '   ''1''::varchar as code';
vQuery := vQuery || ' from query_personal_viewing_data($1, $2, $3, $4, $6, $7) v';
WHEN 'household' THEN
vQuery := vQuery || ' select';
vQuery := vQuery || '   v.*,';
vQuery := vQuery || '   ''1''::varchar as code';
vQuery := vQuery || ' from query_household_viewing_data($1, $2, $3, $4, $6, $7) v';
ELSE
vQuery := vQuery || ' select';
vQuery := vQuery || '   v.*';
vQuery := vQuery || ' from query_personal_viewing_data_with_attr($1, $2, $3, $5, $4, $6, $7) v';
END CASE;
vQuery := vQuery || '   ) v';
vQuery := vQuery || '   where v.code is not null';
vQuery := vQuery || '   group by v.term_from, v.term_to, v.time_box_id, v.channel_id, v.code';
vQuery := vQuery || ' ) q1';
--オリジナル区分の場合は有効パネル数を計算する
vQuery := vQuery || ' left join';
IF pDivision NOT IN ('personal', 'household') THEN
vQuery := vQuery || ' query_samples(q1.time_box_id, $5)';
ELSE
vQuery := vQuery || ' time_box_attr_numbers';
END IF;
vQuery := vQuery || ' tn on tn.time_box_id = q1.time_box_id and tn.division = $5 and tn.code = q1.code';
vQuery := vQuery || ' left join (';
vQuery := vQuery || '   select';
vQuery := vQuery || '     id,';
vQuery := vQuery || '     extract(epoch from (least(ended_at, $3) - greatest(started_at, $2))) as seconds';
vQuery := vQuery || '   from time_boxes';
vQuery := vQuery || '   where region_id = $1 and started_at < $3 and ended_at > $2';
vQuery := vQuery || ' ) tbq on tbq.id = q1.time_box_id';
vQuery := vQuery || ' group by q1.term_from, q1.term_to, q1.channel_id, q1.code';
--RAISE NOTICE '%', vQuery;

--実行＆返却
RETURN QUERY
EXECUTE vQuery
USING pRegionId, pTermFrom, pTermTo, pChannelId, pDivision, pSplitInterval, pChannelConversionMode
;
RETURN;
END;
$_$;


ALTER FUNCTION public.query_viewing_rate(pregionid integer, ptermfrom timestamp without time zone, ptermto timestamp without time zone, pchannelid integer, pdivision character varying, psplitinterval interval, pchannelconversionmode text) OWNER TO switch;

--
-- Name: to_frame_timestamp(timestamp without time zone); Type: FUNCTION; Schema: public; Owner: switch
--

CREATE FUNCTION public.to_frame_timestamp(ptimestamp timestamp without time zone) RETURNS timestamp without time zone
    LANGUAGE plpgsql
    AS $$
DECLARE
vSecond integer;
vResult timestamp;
BEGIN
IF pTimestamp IS NULL THEN
RETURN NULL;
END IF;

vSecond := trunc(extract(second from pTimestamp));
vSecond := vSecond / 15 * 15;

vResult := date_trunc('minute', pTimestamp) + (vSecond || ' second')::interval;

RETURN vResult;
END;
$$;


ALTER FUNCTION public.to_frame_timestamp(ptimestamp timestamp without time zone) OWNER TO switch;

--
-- Name: seq_administrator_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_administrator_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_administrator_id OWNER TO switch;

--
-- Name: administrators; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.administrators (
    id integer DEFAULT nextval('public.seq_administrator_id'::regclass) NOT NULL,
    email character varying(255) NOT NULL,
    password_digest character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    permission character varying(3) DEFAULT 0 NOT NULL,
    deleted_at timestamp(0) without time zone,
    remember_token character varying(100)
);


ALTER TABLE public.administrators OWNER TO switch;

--
-- Name: seq_announcement_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_announcement_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_announcement_id OWNER TO switch;

--
-- Name: announcements; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.announcements (
    id integer DEFAULT nextval('public.seq_announcement_id'::regclass) NOT NULL,
    content text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.announcements OWNER TO switch;

--
-- Name: seq_answers_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_answers_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_answers_id OWNER TO switch;

--
-- Name: answers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.answers (
    id integer DEFAULT nextval('public.seq_answers_id'::regclass) NOT NULL,
    question_id integer,
    order_number integer,
    content text NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.answers OWNER TO switch;

--
-- Name: api_log; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.api_log (
    api character varying(100) NOT NULL,
    parameter jsonb NOT NULL,
    member_id integer NOT NULL,
    exec_time real NOT NULL,
    date timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.api_log OWNER TO switch;

--
-- Name: api_summary; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.api_summary (
    api character varying(100) NOT NULL,
    exec_count bigint NOT NULL,
    avg_exec_time real NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.api_summary OWNER TO switch;

--
-- Name: attr_divs; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.attr_divs (
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    name text NOT NULL,
    display_order integer NOT NULL,
    definition text,
    color character varying(6),
    population numeric,
    weight numeric,
    restore_info text,
    restore_info_text text,
    base_samples smallint
);


ALTER TABLE public.attr_divs OWNER TO switch;

--
-- Name: audience_data; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.audience_data (
    tuner_event_id bigint NOT NULL,
    region_id smallint NOT NULL,
    paneler_id integer NOT NULL,
    base_unit_id integer NOT NULL,
    started_at timestamp(0) without time zone NOT NULL,
    started_usec integer NOT NULL,
    ended_at timestamp(0) without time zone DEFAULT 'infinity'::timestamp without time zone NOT NULL,
    channel_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.audience_data OWNER TO switch;

--
-- Name: audience_data_tmp; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.audience_data_tmp (
    tuner_event_id bigint,
    region_id smallint,
    paneler_id integer NOT NULL,
    base_unit_id integer,
    started_at timestamp(0) without time zone NOT NULL,
    started_usec integer,
    ended_at timestamp(0) without time zone NOT NULL,
    channel_id integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.audience_data_tmp OWNER TO switch;

--
-- Name: seq_base_unit_event_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_base_unit_event_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_base_unit_event_id OWNER TO switch;

--
-- Name: base_unit_events; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.base_unit_events (
    id bigint DEFAULT nextval('public.seq_base_unit_event_id'::regclass) NOT NULL,
    region_id integer NOT NULL,
    base_unit_id integer NOT NULL,
    content text NOT NULL,
    occurred_at timestamp(0) without time zone NOT NULL,
    nsec integer DEFAULT 0 NOT NULL,
    processed integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.base_unit_events OWNER TO switch;

--
-- Name: base_units; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.base_units (
    id integer NOT NULL,
    household_id integer,
    number integer,
    mac_address character varying(255) NOT NULL,
    hardware_version character varying(255),
    command character varying(255),
    revision character varying(255),
    last_contact timestamp(0) without time zone,
    disconnected_at timestamp(0) without time zone,
    remarks text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    region_id integer,
    current_acid_git_tag character varying(255),
    new_acid_git_tag character varying(255),
    irradiation_time real,
    minimum_interval_between_signals real,
    television_set_id integer,
    resend0 timestamp(0) without time zone,
    resend1 timestamp(0) without time zone,
    tv_location character varying(32),
    is_recorder smallint,
    status character varying(255) DEFAULT 'in_use'::character varying
);


ALTER TABLE public.base_units OWNER TO switch;

--
-- Name: seq_base_units_info_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_base_units_info_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_base_units_info_id OWNER TO switch;

--
-- Name: base_units_info; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.base_units_info (
    id integer DEFAULT nextval('public.seq_base_units_info_id'::regclass) NOT NULL,
    starbo_id integer NOT NULL,
    ip_local character varying(50),
    ip_global character varying(50),
    macaddress character varying(50) NOT NULL,
    clock_starbo timestamp without time zone,
    disk_used integer,
    sd_capacity integer,
    load_avg character varying(32),
    uptime character varying(50),
    revision_starbo character varying(50) NOT NULL,
    lcd character varying(50),
    led character varying(50),
    ap_macaddress character varying(50) NOT NULL,
    ap_ssid character varying(50) NOT NULL,
    ap_signal character varying(50) NOT NULL,
    updated_at timestamp without time zone,
    created_at timestamp without time zone,
    region_id smallint,
    remote_control_type character varying(255)
);


ALTER TABLE public.base_units_info OWNER TO switch;

--
-- Name: base_units_trans_work; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.base_units_trans_work (
    id integer NOT NULL,
    household_id integer,
    number integer,
    mac_address character varying(255),
    hardware_version character varying(255) NOT NULL,
    command character varying(255),
    revision character varying(255),
    last_contact timestamp(0) without time zone,
    disconnected_at timestamp(0) without time zone,
    remarks text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    region_id integer,
    current_acid_git_tag character varying(255),
    new_acid_git_tag character varying(255),
    irradiation_time real,
    minimum_interval_between_signals real,
    television_set_id integer,
    resend0 timestamp(0) without time zone,
    resend1 timestamp(0) without time zone,
    tv_location character varying(32),
    is_recorder smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.base_units_trans_work OWNER TO switch;

--
-- Name: seq_batch_control_list_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_batch_control_list_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_batch_control_list_id OWNER TO switch;

--
-- Name: batch_control_lists; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.batch_control_lists (
    id integer DEFAULT nextval('public.seq_batch_control_list_id'::regclass) NOT NULL,
    batch_type character varying(32) NOT NULL,
    command character varying(64) NOT NULL,
    execution_date date NOT NULL,
    description character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.batch_control_lists OWNER TO switch;

--
-- Name: seq_batch_report_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_batch_report_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_batch_report_id OWNER TO switch;

--
-- Name: seq_processing_group; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_processing_group
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_processing_group OWNER TO switch;

--
-- Name: batch_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.batch_reports (
    id integer DEFAULT nextval('public.seq_batch_report_id'::regclass) NOT NULL,
    batch_type character varying(255) NOT NULL,
    processing_group integer DEFAULT nextval('public.seq_processing_group'::regclass) NOT NULL,
    status character varying(255) NOT NULL,
    targets integer,
    changed integer,
    warnings integer,
    errors integer,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.batch_reports OWNER TO switch;

--
-- Name: bs_program_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.bs_program_reports (
    prog_id character varying(32) NOT NULL,
    time_box_id integer NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    viewing_seconds integer NOT NULL,
    viewing_rate real NOT NULL,
    end_viewing_seconds integer,
    end_viewing_rate real
);


ALTER TABLE public.bs_program_reports OWNER TO switch;

--
-- Name: bs_program_viewers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.bs_program_viewers (
    prog_id character varying(32) NOT NULL,
    time_box_id integer NOT NULL,
    paneler_id integer NOT NULL,
    viewing_seconds integer NOT NULL,
    end_viewing_seconds integer
);


ALTER TABLE public.bs_program_viewers OWNER TO switch;

--
-- Name: bs_programs; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.bs_programs (
    prog_id character varying(32),
    time_box_id integer,
    date date,
    started_at timestamp(0) without time zone,
    ended_at timestamp(0) without time zone,
    real_started_at timestamp(0) without time zone,
    real_ended_at timestamp(0) without time zone,
    channel_id integer,
    genre_id character varying(32),
    title character varying(255),
    ts_update timestamp(0) without time zone,
    unknown integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    prepared integer,
    finalized integer,
    calculated_at timestamp(0) without time zone,
    personal_viewing_seconds integer,
    personal_viewing_rate real,
    household_viewing_seconds integer,
    household_viewing_rate real,
    household_viewing_share real,
    household_end_viewing_rate real
);


ALTER TABLE public.bs_programs OWNER TO switch;

--
-- Name: bs_programs_bk; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.bs_programs_bk (
    prog_id character varying(32),
    time_box_id integer,
    date date,
    started_at timestamp(0) without time zone,
    ended_at timestamp(0) without time zone,
    real_started_at timestamp(0) without time zone,
    real_ended_at timestamp(0) without time zone,
    channel_id integer,
    genre_id character varying(32),
    title character varying(255),
    ts_update timestamp(0) without time zone,
    unknown integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    prepared integer,
    finalized integer,
    calculated_at timestamp(0) without time zone,
    personal_viewing_seconds integer,
    personal_viewing_rate real,
    household_viewing_seconds integer,
    household_viewing_rate real,
    household_viewing_share real,
    household_end_viewing_rate real
);


ALTER TABLE public.bs_programs_bk OWNER TO switch;

--
-- Name: seq_channel_number_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_channel_number_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_channel_number_id OWNER TO switch;

--
-- Name: channel_numbers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.channel_numbers (
    id integer DEFAULT nextval('public.seq_channel_number_id'::regclass) NOT NULL,
    region_id integer NOT NULL,
    channel_id integer NOT NULL,
    key character varying(255) NOT NULL,
    branch character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.channel_numbers OWNER TO switch;

--
-- Name: channel_spot_sales; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.channel_spot_sales (
    channel_id integer NOT NULL,
    date date NOT NULL,
    sales integer NOT NULL
);


ALTER TABLE public.channel_spot_sales OWNER TO switch;

--
-- Name: channels; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.channels (
    id integer NOT NULL,
    region_id smallint NOT NULL,
    type character varying(255) DEFAULT 'dt'::character varying NOT NULL,
    button_number integer,
    code_name character varying(255) NOT NULL,
    display_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    "position" integer,
    mdata_service_id character varying(32),
    with_commercials integer DEFAULT 0,
    hdy_channel_code character varying(10),
    hdy_channel_name character varying(10),
    hdy_type_code character varying(10),
    hdy_report_targeted integer DEFAULT 0 NOT NULL,
    report_targeted integer DEFAULT 0 NOT NULL,
    network character varying(32),
    division character varying(20),
    ts_flag smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.channels OWNER TO switch;

--
-- Name: COLUMN channels.ts_flag; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.channels.ts_flag IS 'タイムシフトフラグ';


--
-- Name: cm_chances; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.cm_chances (
    prog_id character varying(32),
    time_box_id integer,
    date date,
    started_at timestamp without time zone,
    ended_at timestamp without time zone,
    channel_id integer,
    title character varying(255)
);


ALTER TABLE public.cm_chances OWNER TO switch;

--
-- Name: cm_company_ranking; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.cm_company_ranking (
    region_id integer NOT NULL,
    ym character varying(10) NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    channel_id integer NOT NULL,
    cm_large_genre character varying(32) NOT NULL,
    cm_type character varying NOT NULL,
    company_id integer NOT NULL,
    times integer NOT NULL,
    duration integer NOT NULL,
    point double precision,
    rate real,
    conv_point double precision,
    conv_rate real
);


ALTER TABLE public.cm_company_ranking OWNER TO switch;

--
-- Name: seq_cm_groups_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_cm_groups_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_cm_groups_id OWNER TO switch;

--
-- Name: cm_groups; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.cm_groups (
    id integer DEFAULT nextval('public.seq_cm_groups_id'::regclass) NOT NULL,
    cm_id character varying(32) NOT NULL,
    co_name text,
    pr_name text,
    first_date date,
    setting text,
    talent text,
    remarks text,
    bgm text,
    memo text,
    duration integer NOT NULL,
    creative text,
    date_from date,
    targeted integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.cm_groups OWNER TO switch;

--
-- Name: cm_product_ranking; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.cm_product_ranking (
    region_id integer NOT NULL,
    ym character varying(10) NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    channel_id integer NOT NULL,
    cm_large_genre character varying(32) NOT NULL,
    cm_type character varying NOT NULL,
    product_id integer NOT NULL,
    times integer NOT NULL,
    duration integer NOT NULL,
    household_point double precision,
    point double precision,
    conv_household_point double precision,
    conv_point double precision,
    tci double precision
);


ALTER TABLE public.cm_product_ranking OWNER TO switch;

--
-- Name: cm_report_work; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.cm_report_work (
    region_id integer NOT NULL,
    time_box_id integer NOT NULL,
    frame_time timestamp(0) without time zone NOT NULL,
    channel_id integer NOT NULL,
    paneler_id integer NOT NULL
);


ALTER TABLE public.cm_report_work OWNER TO switch;

--
-- Name: cm_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.cm_reports (
    cm_id character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    started_at timestamp(0) without time zone NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    viewing_number integer NOT NULL,
    viewing_rate real NOT NULL
);


ALTER TABLE public.cm_reports OWNER TO switch;

--
-- Name: cm_viewers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.cm_viewers (
    region_id integer NOT NULL,
    date date NOT NULL,
    cm_id character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    started_at timestamp(0) without time zone NOT NULL,
    paneler_id integer NOT NULL
);


ALTER TABLE public.cm_viewers OWNER TO switch;

--
-- Name: cm_viewers_sb; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.cm_viewers_sb (
    region_id integer,
    date date,
    cm_id character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    started_at timestamp without time zone NOT NULL,
    paneler_id integer NOT NULL,
    household_id integer,
    code character varying(32),
    fq integer,
    hc integer
);


ALTER TABLE public.cm_viewers_sb OWNER TO switch;

--
-- Name: codes; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.codes (
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    name text NOT NULL,
    display_order integer NOT NULL
);


ALTER TABLE public.codes OWNER TO switch;

--
-- Name: commercials; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.commercials (
    cm_id character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    started_at timestamp(0) without time zone NOT NULL,
    ended_at timestamp(0) without time zone NOT NULL,
    date date NOT NULL,
    region_id integer NOT NULL,
    time_box_id integer NOT NULL,
    channel_id integer NOT NULL,
    company_id integer NOT NULL,
    product_id integer NOT NULL,
    scene_id character varying(32),
    duration integer NOT NULL,
    program_title character varying(255) NOT NULL,
    genre_id character varying(32),
    setting text,
    talent text,
    remarks text,
    bgm text,
    memo text,
    first_date date,
    ts_update timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    calculated_at timestamp(0) without time zone,
    personal_viewing_number integer,
    personal_viewing_rate real,
    household_viewing_number integer,
    household_viewing_rate real,
    cm_type integer DEFAULT 0 NOT NULL,
    cm_type_updated_at timestamp(0) without time zone,
    ts_calculated_at timestamp(0) without time zone,
    ts_personal_viewing_number integer,
    ts_personal_viewing_rate real,
    ts_personal_total_viewing_number integer,
    ts_personal_total_viewing_rate real,
    ts_personal_gross_viewing_number integer,
    ts_personal_gross_viewing_rate real,
    ts_household_viewing_number integer,
    ts_household_viewing_rate real,
    ts_household_total_viewing_number integer,
    ts_household_total_viewing_rate real,
    ts_household_gross_viewing_number integer,
    ts_household_gross_viewing_rate real
);


ALTER TABLE public.commercials OWNER TO switch;

--
-- Name: COLUMN commercials.ts_calculated_at; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_calculated_at IS 'TS視聴率計算日時';


--
-- Name: COLUMN commercials.ts_personal_viewing_number; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_personal_viewing_number IS 'TS個人視聴者数';


--
-- Name: COLUMN commercials.ts_personal_viewing_rate; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_personal_viewing_rate IS 'TS個人視聴率％';


--
-- Name: COLUMN commercials.ts_personal_total_viewing_number; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_personal_total_viewing_number IS 'TS延べ個人視聴者数';


--
-- Name: COLUMN commercials.ts_personal_total_viewing_rate; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_personal_total_viewing_rate IS 'TS延べ個人視聴率％';


--
-- Name: COLUMN commercials.ts_personal_gross_viewing_number; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_personal_gross_viewing_number IS 'TS総合個人視聴者数';


--
-- Name: COLUMN commercials.ts_personal_gross_viewing_rate; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_personal_gross_viewing_rate IS 'TS総合個人視聴率％';


--
-- Name: COLUMN commercials.ts_household_viewing_number; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_household_viewing_number IS 'TS世帯視聴者数';


--
-- Name: COLUMN commercials.ts_household_viewing_rate; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_household_viewing_rate IS 'TS世帯視聴率％';


--
-- Name: COLUMN commercials.ts_household_total_viewing_number; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_household_total_viewing_number IS 'TS延べ世帯視聴者数';


--
-- Name: COLUMN commercials.ts_household_total_viewing_rate; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_household_total_viewing_rate IS 'TS延べ世帯視聴率％';


--
-- Name: COLUMN commercials.ts_household_gross_viewing_number; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_household_gross_viewing_number IS 'TS総合世帯視聴者数';


--
-- Name: COLUMN commercials.ts_household_gross_viewing_rate; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.commercials.ts_household_gross_viewing_rate IS 'TS総合世帯視聴率％';


--
-- Name: seq_company_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_company_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_company_id OWNER TO switch;

--
-- Name: companies; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.companies (
    id integer DEFAULT nextval('public.seq_company_id'::regclass) NOT NULL,
    name text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.companies OWNER TO switch;

--
-- Name: company_group_info; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.company_group_info (
    grp_id integer NOT NULL,
    company_id integer NOT NULL,
    grp_name text,
    grp_flg integer NOT NULL
);


ALTER TABLE public.company_group_info OWNER TO switch;

--
-- Name: seq_control_button_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_control_button_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_control_button_id OWNER TO switch;

--
-- Name: control_buttons; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.control_buttons (
    id integer DEFAULT nextval('public.seq_control_button_id'::regclass) NOT NULL,
    base_unit_id integer NOT NULL,
    channel_id integer,
    type character varying(255) DEFAULT 'dt'::character varying NOT NULL,
    number integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.control_buttons OWNER TO switch;

--
-- Name: dlife_daily_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.dlife_daily_reports (
    prog_id character varying(32) NOT NULL,
    record_type character varying(32) NOT NULL,
    started_at timestamp without time zone,
    ended_at timestamp without time zone,
    duration integer,
    title character varying(255),
    code character varying(32) NOT NULL,
    viewing_seconds integer,
    count integer
);


ALTER TABLE public.dlife_daily_reports OWNER TO switch;

--
-- Name: dummy; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.dummy (
    a character varying(200),
    b character varying(500)
);


ALTER TABLE public.dummy OWNER TO switch;

--
-- Name: enq_answers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.enq_answers (
    paneler_id integer NOT NULL,
    answer_column character varying(255) NOT NULL,
    answer integer
);


ALTER TABLE public.enq_answers OWNER TO switch;

--
-- Name: enq_answers_2014; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.enq_answers_2014 (
    paneler_id integer NOT NULL,
    answer_column character varying(255) NOT NULL,
    answer integer NOT NULL
);


ALTER TABLE public.enq_answers_2014 OWNER TO switch;

--
-- Name: enq_answers_2015; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.enq_answers_2015 (
    paneler_id integer,
    answer_column character varying(255),
    answer integer
);


ALTER TABLE public.enq_answers_2015 OWNER TO switch;

--
-- Name: enq_answers_2016; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.enq_answers_2016 (
    paneler_id integer,
    answer_column character varying(255),
    answer integer
);


ALTER TABLE public.enq_answers_2016 OWNER TO switch;

--
-- Name: enq_answers_2017; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.enq_answers_2017 (
    paneler_id integer,
    answer_column character varying(255),
    answer integer
);


ALTER TABLE public.enq_answers_2017 OWNER TO switch;

--
-- Name: seq_enq_question_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_enq_question_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_enq_question_id OWNER TO switch;

--
-- Name: enq_questions; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.enq_questions (
    id integer DEFAULT nextval('public.seq_enq_question_id'::regclass) NOT NULL,
    q_no character varying(255) NOT NULL,
    item character varying(255),
    label character varying(255),
    answer_column character varying(255),
    q_type character varying(255),
    a_type character varying(255),
    category_no integer,
    column_position integer NOT NULL,
    question character varying(1024),
    option_no integer NOT NULL,
    option character varying(255),
    filter character varying(255),
    q_group character varying(255),
    tag character varying(1024)
);


ALTER TABLE public.enq_questions OWNER TO switch;

--
-- Name: seq_question_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_question_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_question_id OWNER TO switch;

--
-- Name: enq_questions_2014; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.enq_questions_2014 (
    id integer DEFAULT nextval('public.seq_question_id'::regclass),
    q_no character varying(255) NOT NULL,
    item character varying(255) NOT NULL,
    label character varying(255) NOT NULL,
    answer_column character varying(255),
    q_type character varying(255),
    a_type character varying(255),
    category_num integer,
    column_position integer,
    question character varying(1024),
    option_num integer NOT NULL,
    option character varying(255),
    filter character varying(255),
    q_group character varying(255),
    tag character varying(1024)
);


ALTER TABLE public.enq_questions_2014 OWNER TO switch;

--
-- Name: enq_questions_2015; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.enq_questions_2015 (
    id integer,
    q_no character varying(255),
    item character varying(255),
    label character varying(255),
    answer_column character varying(255),
    q_type character varying(255),
    a_type character varying(255),
    category_no integer,
    column_position integer,
    question character varying(1024),
    option_no integer,
    option character varying(255),
    filter character varying(255),
    q_group character varying(255),
    tag character varying(1024)
);


ALTER TABLE public.enq_questions_2015 OWNER TO switch;

--
-- Name: enq_questions_2016; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.enq_questions_2016 (
    id integer,
    q_no character varying(255),
    item character varying(255),
    label character varying(255),
    answer_column character varying(255),
    q_type character varying(255),
    a_type character varying(255),
    category_no integer,
    column_position integer,
    question character varying(1024),
    option_no integer,
    option character varying(255),
    filter character varying(255),
    q_group character varying(255),
    tag character varying(1024)
);


ALTER TABLE public.enq_questions_2016 OWNER TO switch;

--
-- Name: enq_questions_2017; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.enq_questions_2017 (
    id integer DEFAULT nextval('public.seq_enq_question_id'::regclass) NOT NULL,
    q_no character varying(255) NOT NULL,
    item character varying(255),
    label character varying(255),
    answer_column character varying(255),
    q_type character varying(255),
    a_type character varying(255),
    category_no integer,
    column_position integer NOT NULL,
    question character varying(1024),
    option_no integer NOT NULL,
    option character varying(255),
    filter character varying(255),
    q_group character varying(255),
    tag character varying(1024)
);


ALTER TABLE public.enq_questions_2017 OWNER TO switch;

--
-- Name: seq_gaze_rates; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_gaze_rates
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_gaze_rates OWNER TO switch;

--
-- Name: gaze_rates; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.gaze_rates (
    id integer DEFAULT nextval('public.seq_gaze_rates'::regclass) NOT NULL,
    base_unit_id integer NOT NULL,
    paneler_id integer NOT NULL,
    date integer NOT NULL,
    hour smallint NOT NULL,
    min smallint NOT NULL,
    gaze_rate smallint[] NOT NULL,
    created_at timestamp(6) without time zone DEFAULT now() NOT NULL,
    updated_at timestamp(6) without time zone NOT NULL
);


ALTER TABLE public.gaze_rates OWNER TO switch;

--
-- Name: seq_guide_list_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_guide_list_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_guide_list_id OWNER TO switch;

--
-- Name: guide_lists; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.guide_lists (
    id integer DEFAULT nextval('public.seq_guide_list_id'::regclass) NOT NULL,
    processing_group integer NOT NULL,
    guide_type character varying(32) NOT NULL,
    guide_name character varying(255) NOT NULL,
    file_name character varying(255) NOT NULL,
    csv_data text NOT NULL,
    created_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.guide_lists OWNER TO switch;

--
-- Name: seq_holiday_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_holiday_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_holiday_id OWNER TO switch;

--
-- Name: holidays; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.holidays (
    id integer DEFAULT nextval('public.seq_holiday_id'::regclass) NOT NULL,
    holiday date NOT NULL,
    ja_name text,
    en_name text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.holidays OWNER TO switch;

--
-- Name: hourly_avg; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.hourly_avg (
    dow integer NOT NULL,
    hour integer NOT NULL,
    channel_id integer NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    avg_rate real
);


ALTER TABLE public.hourly_avg OWNER TO switch;

--
-- Name: hourly_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.hourly_reports (
    time_box_id integer NOT NULL,
    datetime timestamp(0) without time zone NOT NULL,
    date date NOT NULL,
    hour integer NOT NULL,
    channel_id integer NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    viewing_seconds integer NOT NULL,
    viewing_rate real NOT NULL
);


ALTER TABLE public.hourly_reports OWNER TO switch;

--
-- Name: hourly_viewers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.hourly_viewers (
    region_id integer NOT NULL,
    datetime timestamp(0) without time zone NOT NULL,
    channel_id integer NOT NULL,
    paneler_id integer NOT NULL,
    viewing_seconds integer NOT NULL
);


ALTER TABLE public.hourly_viewers OWNER TO switch;

--
-- Name: seq_household_message_targets; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_household_message_targets
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_household_message_targets OWNER TO switch;

--
-- Name: household_message_targets; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.household_message_targets (
    id integer DEFAULT nextval('public.seq_household_message_targets'::regclass) NOT NULL,
    household_message_id integer,
    paneler_id integer,
    is_respondent boolean DEFAULT false,
    is_complete boolean DEFAULT false,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.household_message_targets OWNER TO switch;

--
-- Name: seq_household_message_variables; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_household_message_variables
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_household_message_variables OWNER TO switch;

--
-- Name: household_message_variables; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.household_message_variables (
    id integer DEFAULT nextval('public.seq_household_message_variables'::regclass) NOT NULL,
    household_message_id integer,
    key character varying(255),
    value character varying(255)
);


ALTER TABLE public.household_message_variables OWNER TO switch;

--
-- Name: seq_household_messages_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_household_messages_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_household_messages_id OWNER TO switch;

--
-- Name: household_messages; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.household_messages (
    id integer DEFAULT nextval('public.seq_household_messages_id'::regclass) NOT NULL,
    household_id integer NOT NULL,
    base_unit_id integer,
    message_id integer NOT NULL,
    is_debug_sendable boolean DEFAULT false NOT NULL,
    is_complete boolean DEFAULT false NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    is_receive boolean DEFAULT false
);


ALTER TABLE public.household_messages OWNER TO switch;

--
-- Name: households; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.households (
    id integer NOT NULL,
    region_id integer NOT NULL,
    name character varying(255) NOT NULL,
    suspended integer DEFAULT 0 NOT NULL,
    suspended_at timestamp(0) without time zone,
    deleted integer DEFAULT 0 NOT NULL,
    deleted_at timestamp(0) without time zone,
    ssid character varying(255),
    ssid_key character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone DEFAULT now(),
    local_ip_addresses character varying(255),
    segment_id integer,
    status character varying(255) NOT NULL,
    remarks text,
    country character varying(255) DEFAULT 'jp'::character varying NOT NULL,
    zip_code character varying(255),
    address1 character varying(255),
    referential_id integer NOT NULL,
    router_type character varying(32),
    details_status character varying(32),
    rank character varying(32),
    registration_on date,
    company_kind smallint DEFAULT 1 NOT NULL
);


ALTER TABLE public.households OWNER TO switch;

--
-- Name: key_keepers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.key_keepers (
    region_id smallint NOT NULL,
    name character varying(255) NOT NULL,
    key_name character varying(255) NOT NULL
);


ALTER TABLE public.key_keepers OWNER TO switch;

--
-- Name: login_failed; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.login_failed (
    login_id character varying(255) NOT NULL,
    info text NOT NULL,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.login_failed OWNER TO switch;

--
-- Name: seq_macromill_monitor_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_macromill_monitor_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_macromill_monitor_id OWNER TO switch;

--
-- Name: macromill_monitors; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.macromill_monitors (
    id integer DEFAULT nextval('public.seq_macromill_monitor_id'::regclass) NOT NULL,
    paneler_id integer NOT NULL,
    sample_id integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.macromill_monitors OWNER TO switch;

--
-- Name: maintenance_messages; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.maintenance_messages (
    id integer NOT NULL,
    message text,
    selected integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.maintenance_messages OWNER TO switch;

--
-- Name: mdata_areas; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.mdata_areas (
    area_id character varying(32) NOT NULL,
    area_name character varying(255) NOT NULL,
    sub_area_name character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.mdata_areas OWNER TO switch;

--
-- Name: mdata_cm_genres; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.mdata_cm_genres (
    genre_id character varying(32) NOT NULL,
    genre1 character varying(255) NOT NULL,
    genre2 character varying(255) NOT NULL,
    genre3 character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    cm_large_genre character varying(32)
);


ALTER TABLE public.mdata_cm_genres OWNER TO switch;

--
-- Name: mdata_products; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.mdata_products (
    product_id character varying(32) NOT NULL,
    genre1 character varying(255) NOT NULL,
    genre2 character varying(255) NOT NULL,
    genre3 character varying(255) NOT NULL,
    genre4 character varying(255) NOT NULL,
    genre5 character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.mdata_products OWNER TO switch;

--
-- Name: mdata_prog_classes; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.mdata_prog_classes (
    class_id character varying(32) NOT NULL,
    name character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.mdata_prog_classes OWNER TO switch;

--
-- Name: mdata_prog_genres; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.mdata_prog_genres (
    genre_id character varying(32) NOT NULL,
    name character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.mdata_prog_genres OWNER TO switch;

--
-- Name: mdata_scenes; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.mdata_scenes (
    scene_id character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    tm_start timestamp(0) without time zone NOT NULL,
    tm_end timestamp(0) without time zone NOT NULL,
    tm_active integer NOT NULL,
    class_id character varying(32) NOT NULL,
    headline character varying(255) NOT NULL,
    memo text,
    dt_create timestamp(0) without time zone NOT NULL,
    dt_update timestamp(0) without time zone NOT NULL,
    num_cm integer NOT NULL,
    num_product integer NOT NULL,
    num_shop integer NOT NULL,
    ts_update timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.mdata_scenes OWNER TO switch;

--
-- Name: mdata_services; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.mdata_services (
    service_id character varying(32) NOT NULL,
    network_id character varying(32) NOT NULL,
    name_normal character varying(255) NOT NULL,
    name_short character varying(255) NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.mdata_services OWNER TO switch;

--
-- Name: member_accesses; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.member_accesses (
    member_id integer NOT NULL,
    login_count integer DEFAULT 0 NOT NULL,
    last_login_at timestamp without time zone,
    login_token character varying(32) NOT NULL
);


ALTER TABLE public.member_accesses OWNER TO switch;

--
-- Name: member_login_logs; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.member_login_logs (
    member_id integer NOT NULL,
    created_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.member_login_logs OWNER TO switch;

--
-- Name: member_original_divs; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.member_original_divs (
    member_id integer NOT NULL,
    menu character varying(32) NOT NULL,
    division character varying(32) NOT NULL,
    target_date_from date NOT NULL,
    target_date_to date NOT NULL,
    display_order integer NOT NULL,
    original_div_edit_flag integer DEFAULT 0 NOT NULL,
    region_id smallint DEFAULT 1 NOT NULL
);


ALTER TABLE public.member_original_divs OWNER TO switch;

--
-- Name: member_system_settings; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.member_system_settings (
    member_id integer NOT NULL,
    conv_15_sec_flag smallint DEFAULT 0 NOT NULL,
    aggregate_setting character varying(30) DEFAULT 'ga12'::character varying NOT NULL,
    aggregate_setting_code text,
    aggregate_setting_region_id integer
);


ALTER TABLE public.member_system_settings OWNER TO switch;

--
-- Name: seq_member_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_member_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_member_id OWNER TO switch;

--
-- Name: members; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.members (
    id integer DEFAULT nextval('public.seq_member_id'::regclass) NOT NULL,
    sponsor_id integer,
    family_name character varying(255) NOT NULL,
    given_name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password_digest character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    remember_token character varying(100),
    login_control_flag integer DEFAULT 1 NOT NULL,
    started_at date NOT NULL,
    ended_at date
);


ALTER TABLE public.members OWNER TO switch;

--
-- Name: members_old; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.members_old (
    id integer NOT NULL,
    sponsor_id integer,
    family_name character varying(255) NOT NULL,
    given_name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password_digest character varying(255),
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    default_aspect character varying(255) DEFAULT 'smart1'::character varying,
    status character varying(255) DEFAULT 'preparing'::character varying NOT NULL,
    trial_started_at date,
    trial_ended_at date,
    remember_token character varying(100),
    login_control_flag integer DEFAULT 1 NOT NULL,
    login_count integer DEFAULT 0 NOT NULL,
    last_login_at timestamp(0) without time zone,
    valid_login_key character varying(32),
    simultaneous_login_count integer DEFAULT 0 NOT NULL,
    conv_15_sec_flag integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.members_old OWNER TO switch;

--
-- Name: seq_message_templates_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_message_templates_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_message_templates_id OWNER TO switch;

--
-- Name: message_templates; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.message_templates (
    id integer DEFAULT nextval('public.seq_message_templates_id'::regclass) NOT NULL,
    title character varying,
    content text,
    created_at timestamp without time zone,
    updated_at timestamp without time zone
);


ALTER TABLE public.message_templates OWNER TO switch;

--
-- Name: seq_messages_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_messages_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_messages_id OWNER TO switch;

--
-- Name: messages; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.messages (
    id integer DEFAULT nextval('public.seq_messages_id'::regclass) NOT NULL,
    title character varying(255) NOT NULL,
    content text NOT NULL,
    started_at timestamp without time zone NOT NULL,
    ended_at timestamp without time zone,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    type smallint DEFAULT 0,
    questionnaire_id integer
);


ALTER TABLE public.messages OWNER TO switch;

--
-- Name: mm_products; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.mm_products (
    pr_id integer NOT NULL,
    type integer NOT NULL,
    co_name text,
    pr_name text
);


ALTER TABLE public.mm_products OWNER TO switch;

--
-- Name: seq_monitor_concern_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_monitor_concern_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_monitor_concern_id OWNER TO switch;

--
-- Name: monitor_concerns; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.monitor_concerns (
    id integer DEFAULT nextval('public.seq_monitor_concern_id'::regclass) NOT NULL,
    household_id integer NOT NULL,
    monitor_concern_type character varying(32) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.monitor_concerns OWNER TO switch;

--
-- Name: obi_programs; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.obi_programs (
    prog_id character varying(32) NOT NULL,
    title character varying(255) NOT NULL,
    title1 character varying(255) NOT NULL,
    title2 character varying(255),
    title3 character varying(255),
    prog_type character varying(255)
);


ALTER TABLE public.obi_programs OWNER TO switch;

--
-- Name: obi_programs_20181004; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.obi_programs_20181004 (
    prog_id character varying(32),
    title character varying(255),
    title1 character varying(255),
    title2 character varying(255),
    title3 character varying(255),
    prog_type character varying(255)
);


ALTER TABLE public.obi_programs_20181004 OWNER TO switch;

--
-- Name: seq_sponsor_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_sponsor_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_sponsor_id OWNER TO switch;

--
-- Name: old_sponsors; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.old_sponsors (
    id integer DEFAULT nextval('public.seq_sponsor_id'::regclass) NOT NULL,
    name character varying(255) NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    status character varying(255) DEFAULT 'preparing'::character varying NOT NULL,
    started_at date,
    ended_at date,
    bs_flag integer DEFAULT 0 NOT NULL,
    realtime_trend_flag integer DEFAULT 0 NOT NULL,
    guest_flag integer DEFAULT 0 NOT NULL,
    before_4week_flag integer DEFAULT 0 NOT NULL,
    overlap_flag integer DEFAULT 0 NOT NULL,
    dashb_pro_started_at date,
    dashb_pro_ended_at date,
    dashb_lite_started_at date,
    dashb_lite_ended_at date,
    dashb_customize_started_at date,
    dashb_customize_ended_at date,
    cm_material_flag integer DEFAULT 0 NOT NULL,
    cm_type_flag integer DEFAULT 0 NOT NULL,
    original_div_edit_flag integer DEFAULT 0 NOT NULL,
    condition_cross_flag integer DEFAULT 0 NOT NULL,
    program_list_extension_flag integer DEFAULT 0 NOT NULL,
    spot_price_flag integer DEFAULT 0 NOT NULL,
    bs_program_flag integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.old_sponsors OWNER TO switch;

--
-- Name: seq_paneler_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_paneler_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_paneler_id OWNER TO switch;

--
-- Name: panelers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.panelers (
    id integer DEFAULT nextval('public.seq_paneler_id'::regclass) NOT NULL,
    region_id integer NOT NULL,
    household_id integer NOT NULL,
    number integer NOT NULL,
    paneler_type integer DEFAULT 0 NOT NULL,
    referential_id integer,
    family_name character varying(255) NOT NULL,
    given_name character varying(255) NOT NULL,
    middle_names character varying(255) NOT NULL,
    gender character varying(255) DEFAULT 'm'::character varying NOT NULL,
    birthday date NOT NULL,
    suspended integer DEFAULT 0 NOT NULL,
    suspended_at timestamp(0) without time zone,
    deleted integer DEFAULT 0 NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    targeted integer DEFAULT 0 NOT NULL,
    primal integer DEFAULT 0 NOT NULL,
    started_on date,
    ended_on date,
    married integer,
    occupation integer,
    div001 integer,
    div002 integer,
    div003 integer,
    div004 integer,
    div005 integer,
    div006 integer,
    div007 integer,
    div008 integer,
    div009 integer,
    div010 integer,
    div011 integer,
    div012 integer,
    div013 integer,
    div014 integer,
    div015 integer,
    div016 integer,
    div017 integer,
    div018 integer,
    div019 integer,
    div020 integer,
    div021 integer,
    div022 integer,
    div023 integer,
    div024 integer,
    div025 integer,
    div026 integer,
    div027 integer,
    div028 integer,
    div029 integer,
    div030 integer,
    div031 integer,
    div032 integer,
    div033 integer,
    div034 integer,
    div035 integer,
    div036 integer,
    div037 integer,
    div038 integer,
    div039 integer,
    div040 integer,
    div041 integer,
    div042 integer,
    div043 integer,
    div044 integer,
    div045 integer,
    div046 integer,
    div047 integer,
    div048 integer,
    div049 integer,
    div050 integer,
    div051 integer,
    div052 integer,
    div053 integer,
    div054 integer,
    div055 integer,
    div056 integer,
    div057 integer,
    div058 integer,
    div059 integer,
    div060 integer,
    div061 integer,
    div062 integer,
    div063 integer,
    div064 integer,
    div065 integer,
    div066 integer,
    div067 integer,
    div068 integer,
    div069 integer,
    div070 integer,
    div071 integer,
    div072 integer,
    div073 integer,
    div074 integer,
    div075 integer,
    div076 integer,
    div077 integer,
    div078 integer,
    div079 integer,
    div080 integer,
    div081 integer,
    div082 integer,
    div083 integer,
    div084 integer,
    div085 integer,
    div086 integer,
    div087 integer,
    div088 integer,
    div089 integer,
    div090 integer,
    div091 integer,
    div092 integer,
    div093 integer,
    div094 integer,
    div095 integer,
    div096 integer,
    div097 integer,
    div098 integer,
    div099 integer,
    div100 integer,
    div101 integer,
    div102 integer,
    div103 integer,
    div104 integer,
    div105 integer,
    div106 integer,
    div107 integer,
    div108 integer,
    div109 integer,
    div110 integer,
    div111 integer,
    div112 integer,
    div113 integer,
    div114 integer,
    div115 integer,
    div116 integer,
    div117 integer,
    div118 integer,
    div119 integer,
    div120 integer,
    div121 integer,
    div122 integer,
    div123 integer,
    div124 integer,
    div125 integer,
    div126 integer,
    div127 integer,
    div128 integer,
    div129 integer,
    div130 integer,
    div131 integer,
    div132 integer,
    div133 integer,
    div134 integer,
    div135 integer,
    div136 integer,
    div137 integer,
    div138 integer,
    div139 integer,
    div140 integer,
    div141 integer,
    div142 integer,
    div143 integer,
    div144 integer,
    div145 integer,
    div146 integer,
    div147 integer,
    div148 integer,
    div149 integer,
    div150 integer,
    div151 integer,
    div152 integer,
    div153 integer,
    div154 integer,
    div155 integer,
    div156 integer,
    div157 integer,
    div158 integer,
    div159 integer,
    div160 integer,
    div161 integer,
    div162 integer,
    div163 integer,
    div164 integer,
    div165 integer,
    div166 integer,
    div167 integer,
    div168 integer,
    div169 integer,
    div170 integer,
    div171 integer,
    div172 integer,
    div173 integer,
    div174 integer,
    div175 integer,
    div176 integer,
    div177 integer,
    div178 integer,
    div179 integer,
    div180 integer,
    div181 integer,
    div182 integer,
    div183 integer,
    div184 integer,
    div185 integer,
    div186 integer,
    div187 integer,
    div188 integer,
    div189 integer,
    div190 integer,
    div191 integer,
    div192 integer,
    div193 integer,
    div194 integer,
    div195 integer,
    div196 integer,
    div197 integer,
    div198 integer,
    div199 integer,
    div200 integer,
    div201 integer,
    div202 integer,
    div203 integer,
    div204 integer,
    div205 integer,
    div206 integer,
    div207 integer,
    div208 integer,
    div209 integer,
    div210 integer,
    div211 integer,
    div212 integer,
    div213 integer,
    div214 integer,
    div215 integer,
    div216 integer,
    div217 integer,
    div218 integer,
    div219 integer,
    div220 integer,
    div221 integer,
    div222 integer,
    div223 integer,
    div224 integer,
    div225 integer,
    div226 integer,
    div227 integer,
    div228 integer,
    div229 integer,
    div230 integer,
    div231 integer,
    div232 integer,
    div233 integer,
    div234 integer,
    div235 integer,
    div236 integer,
    div237 integer,
    div238 integer,
    div239 integer,
    div240 integer,
    div241 integer,
    div242 integer,
    div243 integer,
    div244 integer,
    div245 integer,
    div246 integer,
    div247 integer,
    div248 integer,
    div249 integer,
    div250 integer,
    div251 integer,
    div252 integer,
    div253 integer,
    div254 integer,
    div255 integer,
    div256 integer,
    div257 integer,
    div258 integer,
    div259 integer,
    div260 integer,
    div261 integer,
    div262 integer,
    div263 integer,
    div264 integer,
    div265 integer,
    div266 integer,
    div267 integer,
    div268 integer,
    div269 integer,
    div270 integer,
    div271 integer,
    div272 integer,
    div273 integer,
    div274 integer,
    div275 integer,
    div276 integer,
    div277 integer,
    div278 integer,
    div279 integer,
    div280 integer,
    div281 integer,
    div282 integer,
    div283 integer,
    div284 integer,
    div285 integer,
    div286 integer,
    div287 integer,
    div288 integer,
    div289 integer,
    div290 integer,
    div291 integer,
    div292 integer,
    div293 integer,
    div294 integer,
    div295 integer,
    div296 integer,
    div297 integer,
    div298 integer,
    div299 integer,
    div300 integer
);


ALTER TABLE public.panelers OWNER TO switch;

--
-- Name: per_minute_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.per_minute_reports (
    time_box_id integer NOT NULL,
    datetime timestamp(0) without time zone NOT NULL,
    date date NOT NULL,
    hour integer NOT NULL,
    minute integer NOT NULL,
    channel_id integer NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    viewing_seconds integer NOT NULL,
    viewing_rate real NOT NULL
);


ALTER TABLE public.per_minute_reports OWNER TO switch;

--
-- Name: per_minute_viewers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.per_minute_viewers (
    region_id integer NOT NULL,
    datetime timestamp(0) without time zone NOT NULL,
    channel_id integer NOT NULL,
    paneler_id integer NOT NULL,
    viewing_seconds integer NOT NULL
);


ALTER TABLE public.per_minute_viewers OWNER TO switch;

--
-- Name: seq_product_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_product_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_product_id OWNER TO switch;

--
-- Name: products; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.products (
    id integer DEFAULT nextval('public.seq_product_id'::regclass) NOT NULL,
    company_id integer NOT NULL,
    name text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.products OWNER TO switch;

--
-- Name: program_ranking; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.program_ranking (
    region_id integer NOT NULL,
    ym character varying(10) NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    time_box_id integer NOT NULL,
    channel_id integer NOT NULL,
    viewing_rate real,
    end_viewing_rate real
);


ALTER TABLE public.program_ranking OWNER TO switch;

--
-- Name: program_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.program_reports (
    prog_id character varying(32) NOT NULL,
    time_box_id integer NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    viewing_seconds integer,
    viewing_rate real,
    end_viewing_seconds integer,
    end_viewing_rate real
);


ALTER TABLE public.program_reports OWNER TO switch;

--
-- Name: program_viewers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.program_viewers (
    prog_id character varying(32) NOT NULL,
    time_box_id integer NOT NULL,
    paneler_id integer NOT NULL,
    viewing_seconds integer NOT NULL,
    end_viewing_seconds integer
);


ALTER TABLE public.program_viewers OWNER TO switch;

--
-- Name: programs; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.programs (
    prog_id character varying(32) NOT NULL,
    time_box_id integer NOT NULL,
    date date,
    started_at timestamp(0) without time zone,
    ended_at timestamp(0) without time zone,
    real_started_at timestamp(0) without time zone,
    real_ended_at timestamp(0) without time zone,
    channel_id integer,
    genre_id character varying(32),
    title character varying(255),
    ts_update timestamp(0) without time zone,
    unknown integer DEFAULT 0,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    prepared integer DEFAULT 0,
    finalized integer DEFAULT 0,
    calculated_at timestamp(0) without time zone,
    personal_viewing_seconds integer,
    personal_viewing_rate real,
    household_viewing_seconds integer,
    household_viewing_rate real,
    household_viewing_share real,
    household_end_viewing_rate real,
    ts_calculated_at timestamp(0) without time zone,
    ts_personal_viewing_seconds integer,
    ts_personal_viewing_rate real,
    ts_personal_total_viewing_seconds integer,
    ts_personal_total_viewing_rate real,
    ts_personal_gross_viewing_seconds integer,
    ts_personal_gross_viewing_rate real,
    ts_household_viewing_seconds integer,
    ts_household_viewing_rate real,
    ts_household_total_viewing_seconds integer,
    ts_household_total_viewing_rate real,
    ts_household_gross_viewing_seconds integer,
    ts_household_gross_viewing_rate real
);


ALTER TABLE public.programs OWNER TO switch;

--
-- Name: COLUMN programs.ts_calculated_at; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_calculated_at IS 'TS視聴率計算日時';


--
-- Name: COLUMN programs.ts_personal_viewing_seconds; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_personal_viewing_seconds IS 'TS個人視聴秒数';


--
-- Name: COLUMN programs.ts_personal_viewing_rate; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_personal_viewing_rate IS 'TS個人視聴率％';


--
-- Name: COLUMN programs.ts_personal_total_viewing_seconds; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_personal_total_viewing_seconds IS 'TS延べ個人視聴秒数';


--
-- Name: COLUMN programs.ts_personal_total_viewing_rate; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_personal_total_viewing_rate IS 'TS延べ個人視聴率％';


--
-- Name: COLUMN programs.ts_personal_gross_viewing_seconds; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_personal_gross_viewing_seconds IS 'TS総合個人視聴秒数';


--
-- Name: COLUMN programs.ts_personal_gross_viewing_rate; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_personal_gross_viewing_rate IS 'TS総合個人視聴率％';


--
-- Name: COLUMN programs.ts_household_viewing_seconds; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_household_viewing_seconds IS 'TS世帯視聴秒数';


--
-- Name: COLUMN programs.ts_household_viewing_rate; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_household_viewing_rate IS 'TS世帯視聴率％';


--
-- Name: COLUMN programs.ts_household_total_viewing_seconds; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_household_total_viewing_seconds IS 'TS延べ世帯視聴秒数';


--
-- Name: COLUMN programs.ts_household_total_viewing_rate; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_household_total_viewing_rate IS 'TS延べ世帯視聴率％';


--
-- Name: COLUMN programs.ts_household_gross_viewing_seconds; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_household_gross_viewing_seconds IS 'TS総合世帯視聴秒数';


--
-- Name: COLUMN programs.ts_household_gross_viewing_rate; Type: COMMENT; Schema: public; Owner: switch
--

COMMENT ON COLUMN public.programs.ts_household_gross_viewing_rate IS 'TS総合世帯視聴率％';


--
-- Name: prompt_programs; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.prompt_programs (
    channel_id integer NOT NULL,
    date24 date,
    started_at timestamp without time zone NOT NULL,
    ended_at timestamp without time zone NOT NULL,
    title character varying(255),
    processed integer,
    mail_to text
);


ALTER TABLE public.prompt_programs OWNER TO switch;

--
-- Name: seq_questionnaire_answers_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_questionnaire_answers_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_questionnaire_answers_id OWNER TO switch;

--
-- Name: questionnaire_answers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.questionnaire_answers (
    id integer DEFAULT nextval('public.seq_questionnaire_answers_id'::regclass) NOT NULL,
    questionnaire_id integer,
    question_id integer,
    paneler_id integer,
    answer_id integer,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    paneler_answer_id integer,
    household_message_id integer
);


ALTER TABLE public.questionnaire_answers OWNER TO switch;

--
-- Name: seq_questionnaires_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_questionnaires_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_questionnaires_id OWNER TO switch;

--
-- Name: questionnaires; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.questionnaires (
    id integer DEFAULT nextval('public.seq_questionnaires_id'::regclass) NOT NULL,
    title character varying(255) NOT NULL,
    started_at timestamp without time zone NOT NULL,
    ended_at timestamp without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.questionnaires OWNER TO switch;

--
-- Name: seq_questions_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_questions_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_questions_id OWNER TO switch;

--
-- Name: questions; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.questions (
    id integer DEFAULT nextval('public.seq_questions_id'::regclass) NOT NULL,
    questionnaire_id integer,
    order_number integer,
    content text NOT NULL,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    type smallint DEFAULT 0
);


ALTER TABLE public.questions OWNER TO switch;

--
-- Name: realtime_events; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.realtime_events (
    household_id integer,
    channel character varying(32),
    recorded_at timestamp without time zone,
    broadcasted_at timestamp without time zone,
    paneler_ids character varying(255),
    distance character varying(255),
    rssi character varying(255),
    timeshiftflag smallint,
    matchflag smallint,
    area character varying(255),
    speed integer
);


ALTER TABLE public.realtime_events OWNER TO switch;

--
-- Name: recording_data; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.recording_data (
    recording_event_id bigint NOT NULL,
    region_id smallint NOT NULL,
    paneler_id integer NOT NULL,
    base_unit_id integer NOT NULL,
    started_at timestamp without time zone NOT NULL,
    ended_at timestamp without time zone NOT NULL,
    channel_id integer NOT NULL,
    played_started_at timestamp without time zone NOT NULL,
    played_ended_at timestamp without time zone NOT NULL,
    speed integer NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


ALTER TABLE public.recording_data OWNER TO switch;

--
-- Name: seq_recording_event_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_recording_event_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_recording_event_id OWNER TO switch;

--
-- Name: recording_events; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.recording_events (
    id bigint DEFAULT nextval('public.seq_recording_event_id'::regclass) NOT NULL,
    region_id smallint NOT NULL,
    paneler_id integer NOT NULL,
    channel_id integer NOT NULL,
    base_unit_id integer NOT NULL,
    broadcasted_at timestamp without time zone NOT NULL,
    played_at timestamp(0) without time zone NOT NULL,
    speed integer NOT NULL,
    processed smallint DEFAULT 0 NOT NULL,
    recorded_at timestamp without time zone,
    updated_at timestamp without time zone,
    parent_recording_event_id bigint,
    cleaned smallint DEFAULT 0 NOT NULL,
    rerun smallint DEFAULT 0 NOT NULL,
    record_type smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.recording_events OWNER TO switch;

--
-- Name: seq_recording_event_no_paneler_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_recording_event_no_paneler_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_recording_event_no_paneler_id OWNER TO switch;

--
-- Name: recording_events_no_paneler; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.recording_events_no_paneler (
    id bigint DEFAULT nextval('public.seq_recording_event_no_paneler_id'::regclass) NOT NULL,
    region_id smallint NOT NULL,
    channel_id integer NOT NULL,
    base_unit_id integer NOT NULL,
    broadcasted_at timestamp(0) without time zone NOT NULL,
    played_at timestamp(0) without time zone NOT NULL,
    speed integer NOT NULL,
    processed smallint DEFAULT 0 NOT NULL,
    recorded_at timestamp(0) without time zone,
    record_type smallint DEFAULT 0 NOT NULL,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.recording_events_no_paneler OWNER TO switch;

--
-- Name: recurring_tasks; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.recurring_tasks (
    region_id integer NOT NULL,
    name character varying(255) NOT NULL,
    status character varying(255) NOT NULL,
    started_at timestamp(0) without time zone,
    ended_at timestamp(0) without time zone
);


ALTER TABLE public.recurring_tasks OWNER TO switch;

--
-- Name: seq_region_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_region_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_region_id OWNER TO switch;

--
-- Name: regions; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.regions (
    id integer DEFAULT nextval('public.seq_region_id'::regclass) NOT NULL,
    code_name character varying(255) NOT NULL,
    display_name character varying(255) NOT NULL,
    time_zone character varying(255) DEFAULT 'Tokyo'::character varying NOT NULL,
    date_boundary integer DEFAULT 5 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.regions OWNER TO switch;

--
-- Name: seq_sametime_login_log; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_sametime_login_log
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_sametime_login_log OWNER TO switch;

--
-- Name: sametime_login_log; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.sametime_login_log (
    id integer DEFAULT nextval('public.seq_sametime_login_log'::regclass) NOT NULL,
    member_id integer DEFAULT 0 NOT NULL,
    created_at timestamp without time zone DEFAULT now()
);


ALTER TABLE public.sametime_login_log OWNER TO switch;

--
-- Name: sb_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.sb_reports (
    cm_id character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    started_at timestamp without time zone NOT NULL,
    channel character varying(32),
    cm_type character varying(32),
    duration integer,
    program_title character varying(255),
    product_name text,
    code character varying(32) NOT NULL,
    hh_grp real,
    hh_grp_acm real,
    grp real,
    grp_acm real,
    fq01 real,
    fq02 real,
    fq03 real,
    fq04 real,
    fq05 real,
    fq06 real,
    fq07 real,
    fq08 real,
    fq09 real,
    fq10 real,
    fq11 real,
    fq12 real,
    fq13 real,
    fq14 real,
    fq15 real,
    fq16 real,
    fq17 real,
    fq18 real,
    fq19 real,
    fq20 real,
    fq01_acm real,
    fq02_acm real,
    fq03_acm real,
    fq04_acm real,
    fq05_acm real,
    fq06_acm real,
    fq07_acm real,
    fq08_acm real,
    fq09_acm real,
    fq10_acm real,
    fq11_acm real,
    fq12_acm real,
    fq13_acm real,
    fq14_acm real,
    fq15_acm real,
    fq16_acm real,
    fq17_acm real,
    fq18_acm real,
    fq19_acm real,
    fq20_acm real
);


ALTER TABLE public.sb_reports OWNER TO switch;

--
-- Name: segments; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.segments (
    id integer NOT NULL,
    region_id integer NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.segments OWNER TO switch;

--
-- Name: seq_bs_prog_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_bs_prog_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_bs_prog_id OWNER TO switch;

--
-- Name: seq_business_holiday_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_business_holiday_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_business_holiday_id OWNER TO switch;

--
-- Name: seq_channel_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_channel_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_channel_id OWNER TO switch;

--
-- Name: seq_division_master_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_division_master_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_division_master_id OWNER TO switch;

--
-- Name: seq_gross_attention_numbers; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_gross_attention_numbers
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_gross_attention_numbers OWNER TO switch;

--
-- Name: seq_gross_attention_points; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_gross_attention_points
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_gross_attention_points OWNER TO switch;

--
-- Name: seq_gross_attention_realtime; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_gross_attention_realtime
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_gross_attention_realtime OWNER TO switch;

--
-- Name: seq_support_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_support_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_support_id OWNER TO switch;

--
-- Name: seq_system_notices; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_system_notices
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_system_notices OWNER TO switch;

--
-- Name: seq_television_set_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_television_set_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_television_set_id OWNER TO switch;

--
-- Name: seq_time_box_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_time_box_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_time_box_id OWNER TO switch;

--
-- Name: seq_user_notices; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_user_notices
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_user_notices OWNER TO switch;

--
-- Name: seq_weekly_batch_report_id; Type: SEQUENCE; Schema: public; Owner: switch
--

CREATE SEQUENCE public.seq_weekly_batch_report_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.seq_weekly_batch_report_id OWNER TO switch;

--
-- Name: sim_cm_viewers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.sim_cm_viewers (
    region_id integer,
    date date,
    cm_id character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    started_at timestamp without time zone NOT NULL,
    paneler_id integer NOT NULL
);


ALTER TABLE public.sim_cm_viewers OWNER TO switch;

--
-- Name: sim_commercials; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.sim_commercials (
    cm_id character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    started_at timestamp without time zone NOT NULL,
    ended_at timestamp without time zone,
    date date,
    region_id integer,
    time_box_id integer,
    channel_id integer,
    company_id integer,
    product_id integer,
    scene_id character varying(32),
    duration integer,
    household_viewing_rate real,
    delflag smallint NOT NULL
);


ALTER TABLE public.sim_commercials OWNER TO switch;

--
-- Name: sponsor_roles; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.sponsor_roles (
    sponsor_id integer NOT NULL,
    permissions jsonb NOT NULL
);


ALTER TABLE public.sponsor_roles OWNER TO switch;

--
-- Name: sponsor_trials; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.sponsor_trials (
    sponsor_id integer NOT NULL,
    settings jsonb NOT NULL
);


ALTER TABLE public.sponsor_trials OWNER TO switch;

--
-- Name: sponsors; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.sponsors (
    id integer DEFAULT nextval('public.seq_sponsor_id'::regclass) NOT NULL,
    name character varying(255) NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    status character varying(255) DEFAULT 'preparing'::character varying NOT NULL,
    started_at date NOT NULL,
    ended_at date,
    disp_name character varying(255) DEFAULT ''::character varying NOT NULL,
    sales character varying(255),
    contract_period character varying(255),
    renewal_date date,
    auto_renewal smallint DEFAULT 0 NOT NULL
);


ALTER TABLE public.sponsors OWNER TO switch;

--
-- Name: supports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.supports (
    id integer DEFAULT nextval('public.seq_support_id'::regclass) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL,
    household_id integer NOT NULL,
    support_status character varying(255) NOT NULL,
    support_type character varying(32),
    support_content text,
    scheduled_date date
);


ALTER TABLE public.supports OWNER TO switch;

--
-- Name: system_informations; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.system_informations (
    name character varying(255) NOT NULL,
    is_maintenance smallint DEFAULT 0 NOT NULL,
    updated_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.system_informations OWNER TO switch;

--
-- Name: system_notices; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.system_notices (
    id integer DEFAULT nextval('public.seq_system_notices'::regclass) NOT NULL,
    subject character varying(100) NOT NULL,
    body text NOT NULL,
    imp_level smallint NOT NULL,
    notice_start timestamp without time zone NOT NULL,
    notice_end timestamp without time zone,
    update_user integer NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


ALTER TABLE public.system_notices OWNER TO switch;

--
-- Name: system_notices_read; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.system_notices_read (
    notice_id integer NOT NULL,
    member_id integer NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


ALTER TABLE public.system_notices_read OWNER TO switch;

--
-- Name: television_sets; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.television_sets (
    id integer DEFAULT nextval('public.seq_television_set_id'::regclass) NOT NULL,
    household_id integer NOT NULL,
    tv_model_name character varying(255),
    remote_control_type character varying(255) NOT NULL,
    remote_control_model_name character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    tv_brand_number integer DEFAULT 12 NOT NULL
);


ALTER TABLE public.television_sets OWNER TO switch;

--
-- Name: time_box_attr_numbers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.time_box_attr_numbers (
    time_box_id integer NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    number integer NOT NULL
);


ALTER TABLE public.time_box_attr_numbers OWNER TO switch;

--
-- Name: time_box_channels; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.time_box_channels (
    time_box_id integer NOT NULL,
    channel_id integer NOT NULL
);


ALTER TABLE public.time_box_channels OWNER TO switch;

--
-- Name: time_box_households; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.time_box_households (
    time_box_id integer NOT NULL,
    household_id integer NOT NULL,
    zip_code character varying(255),
    address1 character varying(255)
);


ALTER TABLE public.time_box_households OWNER TO switch;

--
-- Name: time_box_panelers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.time_box_panelers (
    time_box_id integer NOT NULL,
    paneler_id integer NOT NULL,
    household_id integer NOT NULL,
    gender character varying(255) NOT NULL,
    birthday date NOT NULL,
    primal integer NOT NULL,
    age integer NOT NULL,
    married integer,
    occupation integer,
    div001 integer,
    div002 integer,
    div003 integer,
    div004 integer,
    div005 integer,
    div006 integer,
    div007 integer,
    div008 integer,
    div009 integer,
    div010 integer,
    div011 integer,
    div012 integer,
    div013 integer,
    div014 integer,
    div015 integer,
    div016 integer,
    div017 integer,
    div018 integer,
    div019 integer,
    div020 integer,
    div021 integer,
    div022 integer,
    div023 integer,
    div024 integer,
    div025 integer,
    div026 integer,
    div027 integer,
    div028 integer,
    div029 integer,
    div030 integer,
    div031 integer,
    div032 integer,
    div033 integer,
    div034 integer,
    div035 integer,
    div036 integer,
    div037 integer,
    div038 integer,
    div039 integer,
    div040 integer,
    div041 integer,
    div042 integer,
    div043 integer,
    div044 integer,
    div045 integer,
    div046 integer,
    div047 integer,
    div048 integer,
    div049 integer,
    div050 integer,
    div051 integer,
    div052 integer,
    div053 integer,
    div054 integer,
    div055 integer,
    div056 integer,
    div057 integer,
    div058 integer,
    div059 integer,
    div060 integer,
    div061 integer,
    div062 integer,
    div063 integer,
    div064 integer,
    div065 integer,
    div066 integer,
    div067 integer,
    div068 integer,
    div069 integer,
    div070 integer,
    div071 integer,
    div072 integer,
    div073 integer,
    div074 integer,
    div075 integer,
    div076 integer,
    div077 integer,
    div078 integer,
    div079 integer,
    div080 integer,
    div081 integer,
    div082 integer,
    div083 integer,
    div084 integer,
    div085 integer,
    div086 integer,
    div087 integer,
    div088 integer,
    div089 integer,
    div090 integer,
    div091 integer,
    div092 integer,
    div093 integer,
    div094 integer,
    div095 integer,
    div096 integer,
    div097 integer,
    div098 integer,
    div099 integer,
    div100 integer,
    div101 integer,
    div102 integer,
    div103 integer,
    div104 integer,
    div105 integer,
    div106 integer,
    div107 integer,
    div108 integer,
    div109 integer,
    div110 integer,
    div111 integer,
    div112 integer,
    div113 integer,
    div114 integer,
    div115 integer,
    div116 integer,
    div117 integer,
    div118 integer,
    div119 integer,
    div120 integer,
    div121 integer,
    div122 integer,
    div123 integer,
    div124 integer,
    div125 integer,
    div126 integer,
    div127 integer,
    div128 integer,
    div129 integer,
    div130 integer,
    div131 integer,
    div132 integer,
    div133 integer,
    div134 integer,
    div135 integer,
    div136 integer,
    div137 integer,
    div138 integer,
    div139 integer,
    div140 integer,
    div141 integer,
    div142 integer,
    div143 integer,
    div144 integer,
    div145 integer,
    div146 integer,
    div147 integer,
    div148 integer,
    div149 integer,
    div150 integer,
    div151 integer,
    div152 integer,
    div153 integer,
    div154 integer,
    div155 integer,
    div156 integer,
    div157 integer,
    div158 integer,
    div159 integer,
    div160 integer,
    div161 integer,
    div162 integer,
    div163 integer,
    div164 integer,
    div165 integer,
    div166 integer,
    div167 integer,
    div168 integer,
    div169 integer,
    div170 integer,
    div171 integer,
    div172 integer,
    div173 integer,
    div174 integer,
    div175 integer,
    div176 integer,
    div177 integer,
    div178 integer,
    div179 integer,
    div180 integer,
    div181 integer,
    div182 integer,
    div183 integer,
    div184 integer,
    div185 integer,
    div186 integer,
    div187 integer,
    div188 integer,
    div189 integer,
    div190 integer,
    div191 integer,
    div192 integer,
    div193 integer,
    div194 integer,
    div195 integer,
    div196 integer,
    div197 integer,
    div198 integer,
    div199 integer,
    div200 integer,
    div201 integer,
    div202 integer,
    div203 integer,
    div204 integer,
    div205 integer,
    div206 integer,
    div207 integer,
    div208 integer,
    div209 integer,
    div210 integer,
    div211 integer,
    div212 integer,
    div213 integer,
    div214 integer,
    div215 integer,
    div216 integer,
    div217 integer,
    div218 integer,
    div219 integer,
    div220 integer,
    div221 integer,
    div222 integer,
    div223 integer,
    div224 integer,
    div225 integer,
    div226 integer,
    div227 integer,
    div228 integer,
    div229 integer,
    div230 integer,
    div231 integer,
    div232 integer,
    div233 integer,
    div234 integer,
    div235 integer,
    div236 integer,
    div237 integer,
    div238 integer,
    div239 integer,
    div240 integer,
    div241 integer,
    div242 integer,
    div243 integer,
    div244 integer,
    div245 integer,
    div246 integer,
    div247 integer,
    div248 integer,
    div249 integer,
    div250 integer,
    div251 integer,
    div252 integer,
    div253 integer,
    div254 integer,
    div255 integer,
    div256 integer,
    div257 integer,
    div258 integer,
    div259 integer,
    div260 integer,
    div261 integer,
    div262 integer,
    div263 integer,
    div264 integer,
    div265 integer,
    div266 integer,
    div267 integer,
    div268 integer,
    div269 integer,
    div270 integer,
    div271 integer,
    div272 integer,
    div273 integer,
    div274 integer,
    div275 integer,
    div276 integer,
    div277 integer,
    div278 integer,
    div279 integer,
    div280 integer,
    div281 integer,
    div282 integer,
    div283 integer,
    div284 integer,
    div285 integer,
    div286 integer,
    div287 integer,
    div288 integer,
    div289 integer,
    div290 integer,
    div291 integer,
    div292 integer,
    div293 integer,
    div294 integer,
    div295 integer,
    div296 integer,
    div297 integer,
    div298 integer,
    div299 integer,
    div300 integer
);


ALTER TABLE public.time_box_panelers OWNER TO switch;

--
-- Name: time_boxes; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.time_boxes (
    id integer DEFAULT nextval('public.seq_time_box_id'::regclass) NOT NULL,
    region_id integer NOT NULL,
    start_date date NOT NULL,
    duration integer NOT NULL,
    version integer DEFAULT 0 NOT NULL,
    started_at timestamp(0) without time zone NOT NULL,
    ended_at timestamp(0) without time zone NOT NULL,
    panelers_number integer NOT NULL,
    households_number integer NOT NULL
);


ALTER TABLE public.time_boxes OWNER TO switch;

--
-- Name: time_keepers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.time_keepers (
    region_id integer NOT NULL,
    name character varying(255) NOT NULL,
    datetime timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.time_keepers OWNER TO switch;

--
-- Name: tmp_bs_programs; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.tmp_bs_programs (
    date date,
    started_at timestamp(0) without time zone,
    ended_at timestamp(0) without time zone,
    channel_id integer,
    title character varying(255)
);


ALTER TABLE public.tmp_bs_programs OWNER TO switch;

--
-- Name: toyota_fq; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.toyota_fq (
    active_flag integer NOT NULL,
    start_date date NOT NULL,
    cm_id character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    started_at timestamp without time zone NOT NULL,
    paneler_id integer NOT NULL,
    company_name text,
    product_name text,
    frequency integer
);


ALTER TABLE public.toyota_fq OWNER TO switch;

--
-- Name: toyota_trp; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.toyota_trp (
    start_date date,
    cm_id character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    started_at timestamp without time zone NOT NULL,
    duration integer,
    company_name text,
    product_name text,
    personal real,
    f10s real,
    f20s real,
    f30s real,
    f40s real,
    f50s real,
    f60s real,
    m10s real,
    m20s real,
    m30s real,
    m40s real,
    m50s real,
    m60s real,
    household real,
    code_name character varying(255),
    program_title character varying(255)
);


ALTER TABLE public.toyota_trp OWNER TO switch;

--
-- Name: ts_cm_report_work; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.ts_cm_report_work (
    region_id smallint NOT NULL,
    time_box_id integer NOT NULL,
    frame_time timestamp(0) without time zone NOT NULL,
    channel_id integer NOT NULL,
    paneler_id integer NOT NULL,
    played_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.ts_cm_report_work OWNER TO switch;

--
-- Name: ts_cm_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.ts_cm_reports (
    cm_id character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    started_at timestamp(0) without time zone NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    c_index smallint NOT NULL,
    viewing_number integer,
    viewing_rate real,
    total_viewing_number integer,
    total_viewing_rate real,
    gross_viewing_number integer,
    gross_viewing_rate real
);


ALTER TABLE public.ts_cm_reports OWNER TO switch;

--
-- Name: ts_cm_viewers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.ts_cm_viewers (
    region_id smallint NOT NULL,
    date date NOT NULL,
    cm_id character varying(32) NOT NULL,
    prog_id character varying(32) NOT NULL,
    c_index smallint NOT NULL,
    views integer DEFAULT 0 NOT NULL,
    started_at timestamp(0) without time zone NOT NULL,
    paneler_id integer NOT NULL,
    company_id integer NOT NULL
);


ALTER TABLE public.ts_cm_viewers OWNER TO switch;

--
-- Name: ts_hourly_played_time_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.ts_hourly_played_time_reports (
    time_box_id integer NOT NULL,
    datetime timestamp(0) without time zone NOT NULL,
    date date NOT NULL,
    hour integer NOT NULL,
    channel_id integer NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    viewing_seconds integer NOT NULL,
    viewing_rate real NOT NULL
);


ALTER TABLE public.ts_hourly_played_time_reports OWNER TO switch;

--
-- Name: ts_hourly_played_time_viewers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.ts_hourly_played_time_viewers (
    region_id smallint NOT NULL,
    datetime timestamp(0) without time zone NOT NULL,
    channel_id integer NOT NULL,
    paneler_id integer NOT NULL,
    viewing_seconds integer NOT NULL
);


ALTER TABLE public.ts_hourly_played_time_viewers OWNER TO switch;

--
-- Name: ts_hourly_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.ts_hourly_reports (
    time_box_id integer NOT NULL,
    datetime timestamp(0) without time zone NOT NULL,
    date date NOT NULL,
    hour integer NOT NULL,
    channel_id integer NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    viewing_seconds integer NOT NULL,
    viewing_rate real NOT NULL,
    total_viewing_seconds integer NOT NULL,
    total_viewing_rate real NOT NULL,
    gross_viewing_seconds integer NOT NULL,
    gross_viewing_rate real NOT NULL
);


ALTER TABLE public.ts_hourly_reports OWNER TO switch;

--
-- Name: ts_hourly_viewers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.ts_hourly_viewers (
    region_id smallint NOT NULL,
    datetime timestamp(0) without time zone NOT NULL,
    channel_id integer NOT NULL,
    paneler_id integer NOT NULL,
    viewing_seconds integer NOT NULL,
    total_viewing_seconds integer NOT NULL,
    gross_viewing_seconds integer NOT NULL
);


ALTER TABLE public.ts_hourly_viewers OWNER TO switch;

--
-- Name: ts_program_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.ts_program_reports (
    prog_id character varying(32) NOT NULL,
    time_box_id integer NOT NULL,
    division character varying(32) NOT NULL,
    code character varying(32) NOT NULL,
    c_index smallint NOT NULL,
    viewing_seconds integer,
    viewing_rate real,
    total_viewing_seconds integer,
    total_viewing_rate real,
    gross_viewing_seconds integer,
    gross_viewing_rate real
);


ALTER TABLE public.ts_program_reports OWNER TO switch;

--
-- Name: ts_program_viewers; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.ts_program_viewers (
    prog_id character varying(32) NOT NULL,
    time_box_id integer NOT NULL,
    paneler_id integer NOT NULL,
    c_index smallint NOT NULL,
    viewing_seconds integer NOT NULL,
    total_viewing_seconds integer,
    gross_viewing_seconds integer
);


ALTER TABLE public.ts_program_viewers OWNER TO switch;

--
-- Name: user_notices; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.user_notices (
    id integer NOT NULL,
    member_id integer NOT NULL,
    subject character varying(100) NOT NULL,
    body text NOT NULL,
    imp_level smallint NOT NULL,
    notice_start timestamp without time zone NOT NULL,
    notice_end timestamp without time zone,
    update_user integer NOT NULL,
    updated_at timestamp without time zone NOT NULL
);


ALTER TABLE public.user_notices OWNER TO switch;

--
-- Name: user_notices_read; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.user_notices_read (
    notice_id integer,
    member_id integer,
    updated_at timestamp without time zone
);


ALTER TABLE public.user_notices_read OWNER TO switch;

--
-- Name: weekly_batch_reports; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.weekly_batch_reports (
    id integer DEFAULT nextval('public.seq_weekly_batch_report_id'::regclass) NOT NULL,
    processing_group integer NOT NULL,
    processed integer NOT NULL,
    result_code integer NOT NULL,
    household_id integer NOT NULL,
    referential_id integer NOT NULL,
    base_units character varying(32),
    tuner_events character varying(32),
    old_status character varying(32),
    old_status_name character varying(255),
    old_details_status character varying(32),
    old_details_status_name character varying(255),
    new_status character varying(32),
    new_status_name character varying(255),
    new_details_status character varying(32),
    new_details_status_name character varying(255),
    old_supports text,
    old_support_content text,
    old_support_scheduled_date text,
    new_supports character varying(255),
    new_support_content text,
    new_support_scheduled_date text,
    guide_lists character varying(255),
    guide_type character varying(255),
    created_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.weekly_batch_reports OWNER TO switch;

--
-- Name: weekly_csv_data; Type: TABLE; Schema: public; Owner: switch
--

CREATE TABLE public.weekly_csv_data (
    region_id integer NOT NULL,
    time_box_id integer NOT NULL,
    content text NOT NULL
);


ALTER TABLE public.weekly_csv_data OWNER TO switch;

--
-- Name: audience_data_tmp audience_data_tmp_pkey; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.audience_data_tmp
    ADD CONSTRAINT audience_data_tmp_pkey PRIMARY KEY (paneler_id, channel_id, started_at, ended_at);


--
-- Name: channel_spot_sales channel_spot_sales_pkey; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.channel_spot_sales
    ADD CONSTRAINT channel_spot_sales_pkey PRIMARY KEY (channel_id, date);


--
-- Name: administrators pk_administrators; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.administrators
    ADD CONSTRAINT pk_administrators PRIMARY KEY (id);


--
-- Name: announcements pk_announcements; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.announcements
    ADD CONSTRAINT pk_announcements PRIMARY KEY (id);


--
-- Name: answers pk_answers; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.answers
    ADD CONSTRAINT pk_answers PRIMARY KEY (id);


--
-- Name: enq_answers_2014 pk_answers_2014; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.enq_answers_2014
    ADD CONSTRAINT pk_answers_2014 PRIMARY KEY (paneler_id, answer_column, answer);


--
-- Name: api_summary pk_api_summary; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.api_summary
    ADD CONSTRAINT pk_api_summary PRIMARY KEY (api);


--
-- Name: attr_divs pk_attr_divs; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.attr_divs
    ADD CONSTRAINT pk_attr_divs PRIMARY KEY (division, code);


--
-- Name: audience_data pk_audience_data; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.audience_data
    ADD CONSTRAINT pk_audience_data PRIMARY KEY (tuner_event_id);


--
-- Name: base_unit_events pk_base_unit_events; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.base_unit_events
    ADD CONSTRAINT pk_base_unit_events PRIMARY KEY (id);


--
-- Name: base_units pk_base_units; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.base_units
    ADD CONSTRAINT pk_base_units PRIMARY KEY (id);


--
-- Name: base_units_info pk_base_units_info; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.base_units_info
    ADD CONSTRAINT pk_base_units_info PRIMARY KEY (id);


--
-- Name: batch_control_lists pk_batch_control_lists; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.batch_control_lists
    ADD CONSTRAINT pk_batch_control_lists PRIMARY KEY (id);


--
-- Name: batch_reports pk_batch_reports; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.batch_reports
    ADD CONSTRAINT pk_batch_reports PRIMARY KEY (id);


--
-- Name: bs_program_reports pk_bs_program_reports; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.bs_program_reports
    ADD CONSTRAINT pk_bs_program_reports PRIMARY KEY (prog_id, time_box_id, division, code);


--
-- Name: bs_program_viewers pk_bs_program_viewers; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.bs_program_viewers
    ADD CONSTRAINT pk_bs_program_viewers PRIMARY KEY (prog_id, time_box_id, paneler_id);


--
-- Name: channel_numbers pk_channel_numbers; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.channel_numbers
    ADD CONSTRAINT pk_channel_numbers PRIMARY KEY (id);


--
-- Name: channels pk_channels2; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.channels
    ADD CONSTRAINT pk_channels2 PRIMARY KEY (id);


--
-- Name: cm_company_ranking pk_cm_company_ranking; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.cm_company_ranking
    ADD CONSTRAINT pk_cm_company_ranking PRIMARY KEY (region_id, ym, division, code, channel_id, cm_large_genre, cm_type, company_id);


--
-- Name: cm_groups pk_cm_groups; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.cm_groups
    ADD CONSTRAINT pk_cm_groups PRIMARY KEY (id);


--
-- Name: cm_product_ranking pk_cm_product_ranking; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.cm_product_ranking
    ADD CONSTRAINT pk_cm_product_ranking PRIMARY KEY (region_id, ym, division, code, channel_id, cm_large_genre, cm_type, product_id);


--
-- Name: cm_report_work pk_cm_report_work; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.cm_report_work
    ADD CONSTRAINT pk_cm_report_work PRIMARY KEY (region_id, frame_time, channel_id, paneler_id);


--
-- Name: cm_reports pk_cm_reports; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.cm_reports
    ADD CONSTRAINT pk_cm_reports PRIMARY KEY (cm_id, prog_id, started_at, division, code);


--
-- Name: cm_viewers pk_cm_viewers; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.cm_viewers
    ADD CONSTRAINT pk_cm_viewers PRIMARY KEY (cm_id, prog_id, started_at, paneler_id);


--
-- Name: questionnaire_answers pk_questionnaire_answers; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.questionnaire_answers
    ADD CONSTRAINT pk_questionnaire_answers PRIMARY KEY (id);


--
-- Name: questionnaires pk_questionnaires; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.questionnaires
    ADD CONSTRAINT pk_questionnaires PRIMARY KEY (id);


--
-- Name: questions pk_questions; Type: CONSTRAINT; Schema: public; Owner: switch
--

ALTER TABLE ONLY public.questions
    ADD CONSTRAINT pk_questions PRIMARY KEY (id);


--
-- Name: administrators_email_uindex; Type: INDEX; Schema: public; Owner: switch
--

CREATE UNIQUE INDEX administrators_email_uindex ON public.administrators USING btree (email);


--
-- Name: households_referential_id_uindex; Type: INDEX; Schema: public; Owner: switch
--

CREATE UNIQUE INDEX households_referential_id_uindex ON public.households USING btree (referential_id);


--
-- Name: idx_attr_divs_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_attr_divs_1 ON public.attr_divs USING btree (display_order, code);


--
-- Name: idx_attr_divs_2; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_attr_divs_2 ON public.attr_divs USING btree (base_samples);


--
-- Name: idx_audience_data_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_audience_data_1 ON public.audience_data USING btree (started_at, ended_at, paneler_id);


--
-- Name: idx_audience_data_2; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_audience_data_2 ON public.audience_data USING btree (base_unit_id);


--
-- Name: idx_audience_data_3; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_audience_data_3 ON public.audience_data USING btree (started_at, ended_at, channel_id);


--
-- Name: idx_audience_data_4; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_audience_data_4 ON public.audience_data USING btree (paneler_id, started_at, ended_at);


--
-- Name: idx_base_units_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_base_units_1 ON public.base_units USING btree (mac_address);


--
-- Name: idx_cm_reports_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_cm_reports_1 ON public.cm_reports USING btree (started_at);


--
-- Name: idx_cm_viewers_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_cm_viewers_1 ON public.cm_viewers USING btree (date);


--
-- Name: idx_cm_viewers_2; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_cm_viewers_2 ON public.cm_viewers USING btree (region_id, date);


--
-- Name: idx_cm_viewers_3; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_cm_viewers_3 ON public.cm_viewers USING btree (started_at);


--
-- Name: idx_commercials_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_commercials_1 ON public.commercials USING btree (prog_id);


--
-- Name: idx_commercials_2; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_commercials_2 ON public.commercials USING btree (date);


--
-- Name: idx_commercials_3; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_commercials_3 ON public.commercials USING btree (calculated_at);


--
-- Name: idx_commercials_4; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_commercials_4 ON public.commercials USING btree (region_id, company_id, date);


--
-- Name: idx_commercials_5; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_commercials_5 ON public.commercials USING btree (started_at);


--
-- Name: idx_companies_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_companies_1 ON public.companies USING btree (name);


--
-- Name: idx_enq_answers_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_enq_answers_1 ON public.enq_answers USING btree (answer_column, answer);


--
-- Name: idx_hourly_reports_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_hourly_reports_1 ON public.hourly_reports USING btree (datetime, channel_id);


--
-- Name: idx_hourly_viewers_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_hourly_viewers_1 ON public.hourly_viewers USING btree (datetime, channel_id);


--
-- Name: idx_household_message_targets_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_household_message_targets_1 ON public.household_message_targets USING btree (household_message_id);


--
-- Name: idx_household_message_variables_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_household_message_variables_1 ON public.household_message_variables USING btree (household_message_id);


--
-- Name: idx_household_messages_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_household_messages_1 ON public.household_messages USING btree (household_id);


--
-- Name: idx_household_messages_2; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_household_messages_2 ON public.household_messages USING btree (message_id);


--
-- Name: idx_household_messages_3; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_household_messages_3 ON public.household_messages USING btree (base_unit_id, is_debug_sendable);


--
-- Name: idx_household_messages_4; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_household_messages_4 ON public.household_messages USING btree (is_complete, household_id);


--
-- Name: idx_mdata_scenes_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_mdata_scenes_1 ON public.mdata_scenes USING btree (prog_id, tm_start, tm_end);


--
-- Name: idx_member_login_logs; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_member_login_logs ON public.member_login_logs USING btree (created_at);


--
-- Name: idx_messages_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_messages_1 ON public.messages USING btree (started_at, ended_at);


--
-- Name: idx_panelers_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_panelers_1 ON public.panelers USING btree (region_id, suspended, targeted);


--
-- Name: idx_per_minute_reports_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_per_minute_reports_1 ON public.per_minute_reports USING btree (datetime, channel_id);


--
-- Name: idx_per_minute_viewers_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_per_minute_viewers_1 ON public.per_minute_viewers USING btree (datetime);


--
-- Name: idx_programs_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_programs_1 ON public.programs USING btree (title, date, real_started_at, real_ended_at, channel_id);


--
-- Name: idx_programs_2; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_programs_2 ON public.programs USING btree (date);


--
-- Name: idx_programs_3; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_programs_3 ON public.programs USING btree (ts_update, unknown);


--
-- Name: idx_programs_4; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_programs_4 ON public.programs USING btree (real_ended_at);


--
-- Name: idx_programs_5; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_programs_5 ON public.programs USING btree (time_box_id, date, channel_id);


--
-- Name: idx_programs_6; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_programs_6 ON public.programs USING btree (started_at);


--
-- Name: idx_questionnaires_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_questionnaires_1 ON public.questionnaires USING btree (started_at, ended_at);


--
-- Name: idx_realtime_events; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_realtime_events ON public.realtime_events USING btree (household_id, paneler_ids, channel, broadcasted_at);


--
-- Name: idx_recording_data_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_recording_data_1 ON public.recording_data USING btree (paneler_id, started_at, ended_at);


--
-- Name: idx_recording_data_2; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_recording_data_2 ON public.recording_data USING btree (base_unit_id);


--
-- Name: idx_recording_data_3; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_recording_data_3 ON public.recording_data USING btree (started_at, ended_at, channel_id);


--
-- Name: idx_recording_events_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_recording_events_1 ON public.recording_events USING btree (played_at, processed);


--
-- Name: idx_recording_events_no_paneler_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_recording_events_no_paneler_1 ON public.recording_events_no_paneler USING btree (played_at, processed);


--
-- Name: idx_sametime_login_log_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_sametime_login_log_1 ON public.sametime_login_log USING btree (member_id);


--
-- Name: idx_sametime_login_log_2; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_sametime_login_log_2 ON public.sametime_login_log USING btree (created_at);


--
-- Name: idx_time_box_panelers_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_time_box_panelers_1 ON public.time_box_panelers USING btree (household_id);


--
-- Name: idx_timebox_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_timebox_1 ON public.time_boxes USING btree (started_at, ended_at);


--
-- Name: idx_ts_cm_viewers_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_ts_cm_viewers_1 ON public.ts_cm_viewers USING btree (region_id, date);


--
-- Name: idx_ts_cm_viewers_2; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_ts_cm_viewers_2 ON public.ts_cm_viewers USING btree (date);


--
-- Name: idx_ts_hourly_played_time_reports_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_ts_hourly_played_time_reports_1 ON public.ts_hourly_played_time_reports USING btree (datetime, division);


--
-- Name: idx_ts_hourly_played_time_viewers_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_ts_hourly_played_time_viewers_1 ON public.ts_hourly_played_time_viewers USING btree (datetime, channel_id);


--
-- Name: idx_ts_hourly_reports_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_ts_hourly_reports_1 ON public.ts_hourly_reports USING btree (datetime, division);


--
-- Name: idx_ts_hourly_viewers_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_ts_hourly_viewers_1 ON public.ts_hourly_viewers USING btree (datetime, channel_id);


--
-- Name: idx_tuner_events_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_tuner_events_1 ON public.tuner_events USING btree (occurred_at, processed);


--
-- Name: idx_tuner_events_2; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_tuner_events_2 ON public.tuner_events USING btree (base_unit_id);


--
-- Name: idx_tuner_events_3; Type: INDEX; Schema: public; Owner: switch
--

CREATE INDEX idx_tuner_events_3 ON public.tuner_events USING btree (recorded_at);


--
-- Name: u_members_1; Type: INDEX; Schema: public; Owner: switch
--

CREATE UNIQUE INDEX u_members_1 ON public.members USING btree (email);


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: switch
--

REVOKE ALL ON SCHEMA public FROM rdsadmin;
REVOKE ALL ON SCHEMA public FROM PUBLIC;
GRANT ALL ON SCHEMA public TO switch;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--
