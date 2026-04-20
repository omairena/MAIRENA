create database chartjs10;
use chartjs10;

create table venta(
	id int not null auto_increment primary key,
	date_at date,
	val double
);

insert into venta(date_at,val) value ("2019-01-01",100);
insert into venta(date_at,val) value ("2019-01-02",80);
insert into venta(date_at,val) value ("2019-01-03",205);
insert into venta(date_at,val) value ("2019-01-04",323);
insert into venta(date_at,val) value ("2019-01-05",110);
insert into venta(date_at,val) value ("2019-01-06",40);
insert into venta(date_at,val) value ("2019-01-07",80);
insert into venta(date_at,val) value ("2019-01-08",220);
insert into venta(date_at,val) value ("2019-01-09",95);
insert into venta(date_at,val) value ("2019-01-10",120);
insert into venta(date_at,val) value ("2019-01-11",249);
insert into venta(date_at,val) value ("2019-01-12",157);
insert into venta(date_at,val) value ("2019-01-13",199);
insert into venta(date_at,val) value ("2019-01-14",30);
insert into venta(date_at,val) value ("2019-01-15",290);

