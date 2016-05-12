ALTER TABLE public.api_media
    DROP CONSTRAINT api_media_mime_type_check;

ALTER TABLE public.api_media
  ADD CONSTRAINT api_media_mime_type_check CHECK (mime_type::text = ANY (ARRAY['image/jpeg'::character varying, 'audio/mpeg'::character varying, 'video/mp4'::character varying, 'video/x-m4v'::character varying]::text[]));
