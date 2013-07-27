{% $context['title'] = 'Hello World -- Demo' %}
{%t SetMaster('master') %}

{%t StartSection('content') %}
<!-- some head here -->
{%t EndSection() %}

{%t StartSection('content') %}
Hello {{$world}}
{%t EndSection() %}