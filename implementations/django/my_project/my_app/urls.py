from django.conf.urls import url
from my_app import views

urlpatterns = [
    url(r'^$', views.index, name='index'),
    url(r'^auth/', views.auth, name='auth'),
]