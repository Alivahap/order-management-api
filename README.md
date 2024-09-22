# Uygulama Gereksinimleri

- Docker yüklü olmalıdır.

## Uygulamanın Ayağa Kaldırılması

1. Terminali açın.
2. Aşağıdaki komutu çalıştırın:

   ```bash
   docker-compose up --build

Not: Uygulamayı ilk defa başlattığınızda veri tabanına bağlanamama sorunları yaşayabilirsiniz. Bu, Docker’ın MySQL servisini en son çalıştırmasından kaynaklanmaktadır. Yaklaşık 30 saniye ila 1 dakika sonra sayfayı yeniden yüklediğinizde uygulama düzgün şekilde çalışacaktır.


## API'lar
### GET İstekleri
#### Product API

Ürün verilerini çekmek için:

GET http://localhost:8080/api/product.php


#### Customer API

Müşteri verilerini çekmek için:

GET http://localhost:8080/api/customer.php

#### Order API
Sipariş verilerini çekmek için:

GET http://localhost:8080/api/order.php

#### Discount API
İndirim verilerini çekmek için:

GET http://localhost:8080/api/discount.php


## POST İstekleri

### Order API

Yeni bir sipariş oluşturmak için:
POST http://localhost:8080/api/order.php

**Header:**

Content-Type: application/json

**Body:**
```json
{
    "customerId": 1,
    "items": [
        {
            "productId": 1,
            "quantity": 1
        },
        {
            "productId": 107,
            "quantity": 1
        }
    ]
}
```

Bu API isteği, müşteri için bir sipariş oluşturur. customerId alanı, siparişin hangi müşteriye ait olduğunu belirtir. items alanı sipariş edilen ürünlerin listesini içerir. Her ürün için productId ve quantity bilgileri girilmelidir.



## DELETE İstekleri

### Order API
Belirtilen siparişi silmek için:

DELETE http://localhost:8080/api/order.php?id=23


### Açıklama
`id` parametresi, silinmek istenen siparişin id'sini temsil eder. 







