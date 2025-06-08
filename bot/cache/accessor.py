import redis.asyncio as aioredis


async def get_redis_connection() -> aioredis.Redis:
    return await aioredis.Redis(
        host="redis",
        port=6379,
        db=0
    )

async def save_data_to_redis(key, value):
    redis_client = await get_redis_connection()
    await redis_client.set(f"user:{users_data['id']}:{key}", str(value))
#     await redis_client.set(name=f"{key}", value=value)


async def get_redis_data():
    redis_client = await get_redis_connection()
    user_data = {}
    keys = await redis_client.keys('*')
    for key in keys:
        val = await redis_client.get(key)
        user_data[key.decode] = val.decode() if val else None
    return user_data


async def on_shutdown(redis_client):
    if redis_client:
        await redis_client.close()