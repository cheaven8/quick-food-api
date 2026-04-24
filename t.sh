# curl -X POST http://127.0.0.1:8000/api/register \
# -H "Content-Type: application/json" \
# -d '{
#   "email": "customer0@example.com",
#   "password": "123456",
#   "phone": "0912345678",
#   "role": "customer",
#   "first_name": "John",
#   "last_name": "Doe"
# }'

# curl -X POST http://127.0.0.1:8000/api/register \
# -H "Content-Type: application/json" \
# -d '{
#   "email": "hotel@example.com",
#   "password": "123456",
#   "phone": "0987654321",
#   "role": "hotel",
#   "hotel_name": "Sunrise Hotel",
#   "address": "Addis Ababa"
# }'

# curl -X POST http://127.0.0.1:8000/api/login \
# -H "Content-Type: application/json" \
# -d '{
#   "email": "hotel@example.com",
#   "password": "123456"
  
# }'

# curl -X POST http://127.0.0.1:8000/api/login  \
# -H "Content-Type: application/json" \
# -d '{
#   "email": "customer0@example.com",
#   "password": "123456",
#   "phone": "0912345678",
#   "role": "customer",
#   "first_name": "John",
#   "last_name": "Doe"
# }'


# cheaven888@cloudshell:~/delivery-api$ sh t.sh 
# {"token":"17|P23mf4PDHcdS8Rp74bMzsGt5SxYg4oO87VXUdmzZ7acfa79c","role":"customer","profile":{"id":2,"user_id":6,"first_name":"John","last_name":"Doe","avatar":null,"loyalty_pts":0,"created_at":"2026-04-24T05:47:22.000000Z","updated_at":"2026-04-24T05:47

# curl http://127.0.0.1:8000/api/products

#!/bin/bash

# BASE_URL="http://127.0.0.1:8000/api"

# echo "🚀 Creating Hotel 1..."

# curl -X POST $BASE_URL/register \
# -H "Content-Type: application/json" \
# -d '{
#   "email": "hotel1@test.com",
#   "password": "123456",
#   "phone": "0911111111",
#   "role": "hotel",
#   "hotel_name": "Addis Delight Hotel",
#   "address": "Bole, Addis Ababa"
# }'

# echo -e "\n\n🚀 Creating Hotel 2..."

# curl -X POST $BASE_URL/register \
# -H "Content-Type: application/json" \
# -d '{
#   "email": "hotel2@test.com",
#   "password": "123456",
#   "phone": "0922222222",
#   "role": "hotel",
#   "hotel_name": "Lalibela Taste House",
#   "address": "Kazanchis, Addis Ababa"
# }'

# echo -e "\n\n✅ Done!"



#!/bin/bash
# curl -X POST http://localhost:8000/api/login \
# -H "Content-Type: application/json" \
# -d '{
#   "email": "hotel1@test.com",
#   "password": "123456"
# }'



# curl -X POST http://localhost:8000/api/hotel/products \
# -H "Authorization: Bearer C8T028NSkJbG0MjWM4WPqVzFGtUW7lqXzgYh3PgU4969b45b" \
# -H "Content-Type: application/json" \
# -d '{
#   "name": "Doro Wat",
#   "price": 120,
#   "category": "Ethiopian",
#   "preparation_time": 40,
#   "description": "Spicy chicken stew"
# }'


#!/bin/bash
#!/bin/bash

API="http://localhost:8000/api"

# echo "🚀 Creating Hotel 1..."

# HOTEL1=$(curl -s -X POST $API/register \
# -H "Content-Type: application/json" \
# -d '{
#   "email":"hotel11@test.com",
#   "password":"123456",
#   "phone":"0911134111",
#   "role":"hotel",
#   "hotel_name":"Addis Palace",
#   "address":"Addis Ababa"
# }')

# echo "Hotel1 Response: $HOTEL1"

# TOKEN1=$(echo "$HOTEL1" | grep -o '"token":"[^"]*' | cut -d':' -f2 | tr -d '"')

# echo "Hotel1 Token: $TOKEN1"


# echo "🚀 Creating Hotel 2..."

# HOTEL2=$(curl -s -X POST $API/register \
# -H "Content-Type: application/json" \
# -d '{
#   "email":"hotel12@test.com",
#   "password":"123456",
#   "phone":"092224322",
#   "role":"hotel",
#   "hotel_name":"Bole Grand Hotel",
#   "address":"Bole Addis"
# }')

# echo "Hotel2 Response: $HOTEL2"

# TOKEN2=$(echo "$HOTEL2" | grep -o '"token":"[^"]*' | cut -d':' -f2 | tr -d '"')

# echo "Hotel2 Token: $TOKEN2"


# echo "🍲 Adding foods for Hotel 1..."

# curl -s -X POST $API/hotel/products \
# -H "Authorization: Bearer $TOKEN1" \
# -H "Content-Type: application/json" \
# -d '{"name":"Doro Wat","price":120,"category":"Ethiopian","preparation_time":40,"description":"Spicy chicken stew"}'

# curl -s -X POST $API/hotel/products \
# -H "Authorization: Bearer $TOKEN1" \
# -H "Content-Type: application/json" \
# -d '{"name":"Injera Tibs","price":150,"category":"Ethiopian","preparation_time":30,"description":"Beef tibs"}'

# curl -s -X POST $API/hotel/products \
# -H "Authorization: Bearer $TOKEN1" \
# -H "Content-Type: application/json" \
# -d '{"name":"Shiro","price":80,"category":"Ethiopian","preparation_time":20,"description":"Chickpea stew"}'


# echo "🍲 Adding foods for Hotel 2..."

# curl -s -X POST $API/hotel/products \
# -H "Authorization: Bearer $TOKEN2" \
# -H "Content-Type: application/json" \
# -d '{"name":"Kitfo","price":200,"category":"Ethiopian","preparation_time":25,"description":"Raw minced beef"}'

# curl -s -X POST $API/hotel/products \
# -H "Authorization: Bearer $TOKEN2" \
# -H "Content-Type: application/json" \
# -d '{"name":"Firfir","price":70,"category":"Ethiopian","preparation_time":15,"description":"Injera mixed"}'

# curl -s -X POST $API/hotel/products \
# -H "Authorization: Bearer $TOKEN2" \
# -H "Content-Type: application/json" \
# -d '{"name":"Tibs Special","price":180,"category":"Ethiopian","preparation_time":30,"description":"Spiced beef"}'

# echo "✅ DONE: 6 Ethiopian foods added successfully!"


# curl "http://localhost:8000/api/products?category=Ethiopian"
#!/bin/bash

BASE_URL="http://localhost:8000/api"

echo "🔐 Logging in customer..."

LOGIN_RESPONSE=$(curl -s -X POST $BASE_URL/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "customer@example.com",
    "password": "password123"
  }')

TOKEN=$(echo $LOGIN_RESPONSE | jq -r '.token')

echo "Customer Token: $TOKEN"

echo ""
echo "🛒 Creating Order..."

ORDER_RESPONSE=$(curl -s -X POST $BASE_URL/orders \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "hotel_id": 8,
    "customer_phone": "0912345678",
    "delivery_address": "Addis Ababa Bole",
    "special_instructions": "Call before delivery",
    "items": [
      {
        "product_id": 2,
        "quantity": 1
      },
      {
        "product_id": 3,
        "quantity": 2
      }
    ]
  }')

echo "Order Response:"
echo $ORDER_RESPONSE | jq

ORDER_ID=$(echo $ORDER_RESPONSE | jq -r '.data.id')

echo ""
echo "📦 My Orders..."

curl -s -X GET $BASE_URL/orders/my \
  -H "Authorization: Bearer $TOKEN" | jq

echo ""
echo "🔍 Tracking Order..."

curl -s -X GET $BASE_URL/orders/track/ORD-TEST123 \
  -H "Content-Type: application/json" | jq

echo ""