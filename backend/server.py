import uvicorn
import uuid
from fastapi import FastAPI
from vllm import AsyncLLMEngine, AsyncEngineArgs, SamplingParams
from pydantic import BaseModel

app = FastAPI()

engine_args = AsyncEngineArgs(model="meta-llama/Meta-Llama-3-8B-Instruct", tensor_parallel_size=1, enforce_eager=True)
engine = AsyncLLMEngine.from_engine_args(engine_args)

class UserInput(BaseModel):
    prompt: str

@app.post("/chat")
async def chat(user_input: UserInput):
    system_prompt = (
        "You are 'PocketAI Advisor', a financial buddy for Gen Z. "
        "IMPORTANT RULES: "
        "1. DO NOT introduce yourself. "
        "2. DO NOT use introductory greetings like 'What's good' or 'How's life'. "
        "3. Focus ONLY on giving concise, chill, rojak-style financial advice. Answer mainly in Malay. English is second choice of language. "
        "4. If the user provides a prompt, answer it directly in 2-3 sentences. "
        "5. Use slangs: 'no cap', 'slay', 'red flag', 'bruh' where appropriate."
    )

    
    full_prompt = (
        f"<|begin_of_text|><|start_header_id|>system<|end_header_id|>\n{system_prompt}<|eot_id|>"
        f"<|start_header_id|>user<|end_header_id|>\n{user_input.prompt}<|eot_id|>"
        f"<|start_header_id|>assistant<|end_header_id|>\n"
    )
    
    sampling_params = SamplingParams(temperature=0.7, max_tokens=600)
    
    request_id = str(uuid.uuid4())
    results_generator = engine.generate(full_prompt, sampling_params, request_id)
    
    final_output = ""
    async for request_output in results_generator:
        final_output = request_output.outputs[0].text
        
    return {"response": final_output}

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8000)
